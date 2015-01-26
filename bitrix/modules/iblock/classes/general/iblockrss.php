<?
class CAllIBlockRSS
{
	function GetRSSNodes()
	{
		return array("title", "link", "description", "enclosure", "enclosure_length", "enclosure_type", "category", "pubDate");
	}

	function Delete($IBLOCK_ID)
	{
		global $DB;
		$IBLOCK_ID = IntVal($IBLOCK_ID);
		$DB->Query("DELETE FROM b_iblock_rss WHERE IBLOCK_ID = ".$IBLOCK_ID);
	}

	public static function GetNodeList($IBLOCK_ID)
	{
		global $DB;
		$IBLOCK_ID = IntVal($IBLOCK_ID);
		$arCurNodesRSS = array();
		$db_res = $DB->Query(
			"SELECT NODE, NODE_VALUE ".
			"FROM b_iblock_rss ".
			"WHERE IBLOCK_ID = ".$IBLOCK_ID);
		while ($db_res_arr = $db_res->Fetch())
		{
			$arCurNodesRSS[$db_res_arr["NODE"]] = $db_res_arr["NODE_VALUE"];
		}
		return $arCurNodesRSS;
	}

	function GetNewsEx($SITE, $PORT, $PATH, $QUERY_STR, $bOutChannel = False)
	{
		global $APPLICATION;

		$cacheKey = md5($SITE.$PORT.$PATH.$QUERY_STR);

		$bValid = False;
		$bUpdate = False;
		if ($db_res_arr = CIBlockRSS::GetCache($cacheKey))
		{
			$bUpdate = True;
			if (strlen($db_res_arr["CACHE"])>0)
			{
				if ($db_res_arr["VALID"]=="Y")
				{
					$bValid = True;
					$text = $db_res_arr["CACHE"];
				}
			}
		}

		if (!$bValid)
		{
			$FP = fsockopen($SITE, $PORT, $errno, $errstr, 120);

			if ($FP)
			{
				$strVars = $QUERY_STR;
				$strRequest = "GET ".$PATH.(strlen($strVars) > 0? "?".$strVars: "")." HTTP/1.0\r\n";
				$strRequest.= "User-Agent: BitrixSMRSS\r\n";
				$strRequest.= "Accept: */*\r\n";
				$strRequest.= "Host: $SITE\r\n";
				$strRequest.= "Accept-Language: en\r\n";
				$strRequest.= "\r\n";
				fputs($FP, $strRequest);

				$headers = "";
				while(!feof($FP))
				{
					$line = fgets($FP, 4096);
					if($line == "\r\n")
						break;
					$headers .= $line;
				}

				$text = "";
				while(!feof($FP))
					$text .= fread($FP, 4096);

				$rss_charset = "windows-1251";
				if (preg_match("/<"."\?XML[^>]{1,}encoding=[\"']([^>\"']{1,})[\"'][^>]{0,}\?".">/i", $text, $matches))
				{
					$rss_charset = Trim($matches[1]);
				}
				elseif($headers)
				{
					if(preg_match("#^Content-Type:.*?charset=([a-zA-Z0-9-]+)#m", $headers, $match))
						$rss_charset = $match[1];
				}

				$text = preg_replace("/<!DOCTYPE.*?>/i", "", $text);
				$text = preg_replace("/<"."\\?XML.*?\\?".">/i", "", $text);
				$text = $APPLICATION->ConvertCharset($text, $rss_charset, SITE_CHARSET);

				fclose($FP);
			}
			else
			{
				$text = "";
			}
		}

		if (strlen($text) > 0)
		{
			require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/xml.php");
			$objXML = new CDataXML();
			$res = $objXML->LoadString($text);
			if($res !== false)
			{
				$ar = $objXML->GetArray();
				if (!$bOutChannel)
				{
					if (
						is_array($ar) && isset($ar["rss"])
						&& is_array($ar["rss"]) && isset($ar["rss"]["#"])
						&& is_array($ar["rss"]["#"]) && isset($ar["rss"]["#"]["channel"])
						&& is_array($ar["rss"]["#"]["channel"]) && isset($ar["rss"]["#"]["channel"][0])
						&& is_array($ar["rss"]["#"]["channel"][0]) && isset($ar["rss"]["#"]["channel"][0]["#"])
					)
						$arRes = $ar["rss"]["#"]["channel"][0]["#"];
					else
						$arRes = array();
				}
				else
				{
					if (
						is_array($ar) && isset($ar["rss"])
						&& is_array($ar["rss"]) && isset($ar["rss"]["#"])
					)
						$arRes = $ar["rss"]["#"];
					else
						$arRes = array();
				}

				$arRes["rss_charset"] = strtolower(SITE_CHARSET);

				if (!$bValid)
				{
					$ttl = (strlen($arRes["ttl"][0]["#"]) > 0)? IntVal($arRes["ttl"][0]["#"]): 60;
					CIBlockRSS::UpdateCache($cacheKey, $text, array("minutes" => $ttl), $bUpdate);
				}
			}
			return $arRes;
		}
		else
		{
			return array();
		}
	}

	function GetNews($ID, $LANG, $TYPE, $SITE, $PORT, $PATH, $LIMIT = 0)
	{
		if (IntVal($ID)>0)
		{
			$ID = IntVal($ID);
		}
		else
		{
			$ID = Trim($ID);
		}
		$LANG = Trim($LANG);
		$TYPE = Trim($TYPE);
		$LIMIT = IntVal($LIMIT);

		return CIBlockRSS::GetNewsEx($SITE, $PORT, $PATH, "ID=".$ID."&LANG=".$LANG."&TYPE=".$TYPE."&LIMIT=".$LIMIT);
	}

	function FormatArray(&$arRes, $bOutChannel=false)
	{
		if (!$bOutChannel)
		{
			if(is_array($arRes["title"][0]["#"]))
				$arRes["title"][0]["#"] = $arRes["title"][0]["#"]["cdata-section"][0]["#"];
			if(is_array($arRes["link"][0]["#"]))
				$arRes["link"][0]["#"] = $arRes["link"][0]["#"]["cdata-section"][0]["#"];
			if(is_array($arRes["description"][0]["#"]))
				$arRes["description"][0]["#"] = $arRes["description"][0]["#"]["cdata-section"][0]["#"];

			$arResult = array(
				"title" => $arRes["title"][0]["#"],
				"link" => $arRes["link"][0]["#"],
				"description" => $arRes["description"][0]["#"],
				"lastBuildDate" => $arRes["lastBuildDate"][0]["#"],
				"ttl" => $arRes["ttl"][0]["#"],
			);

			if ($arRes["image"])
			{
				if(is_array($arRes["image"][0]["#"]))
				{
					$arResult["image"]["title"] = $arRes["image"][0]["#"]["title"][0]["#"];
					$arResult["image"]["url"] = $arRes["image"][0]["#"]["url"][0]["#"];
					$arResult["image"]["link"] = $arRes["image"][0]["#"]["link"][0]["#"];
					$arResult["image"]["width"] = $arRes["image"][0]["#"]["width"][0]["#"];
					$arResult["image"]["height"] = $arRes["image"][0]["#"]["height"][0]["#"];
				}
				elseif(is_array($arRes["image"][0]["@"]))
				{
					$arResult["image"]["title"] = $arRes["image"][0]["@"]["title"];
					$arResult["image"]["url"] = $arRes["image"][0]["@"]["url"];
					$arResult["image"]["link"] = $arRes["image"][0]["@"]["link"];
					$arResult["image"]["width"] = $arRes["image"][0]["@"]["width"];
					$arResult["image"]["height"] = $arRes["image"][0]["@"]["height"];
				}
			}

			foreach($arRes["item"] as $i => $arItem)
			{
				if(!is_array($arItem) || !is_array($arItem["#"]))
					continue;

				if(is_array($arItem["#"]["title"][0]["#"]))
					$arItem["#"]["title"][0]["#"] = $arItem["#"]["title"][0]["#"]["cdata-section"][0]["#"];

				if(is_array($arItem["#"]["description"][0]["#"]))
					$arItem["#"]["description"][0]["#"] = $arItem["#"]["description"][0]["#"]["cdata-section"][0]["#"];
				elseif(is_array($arItem["#"]["encoded"][0]["#"]))
					$arItem["#"]["description"][0]["#"] = $arItem["#"]["encoded"][0]["#"]["cdata-section"][0]["#"];
				$arResult["item"][$i]["description"] = $arItem["#"]["description"][0]["#"];

				if(is_array($arItem["#"]["title"][0]["#"]))
					$arItem["#"]["title"][0]["#"] = $arItem["#"]["title"][0]["#"]["cdata-section"][0]["#"];
				$arResult["item"][$i]["title"] = $arItem["#"]["title"][0]["#"];

				if(is_array($arItem["#"]["link"][0]["#"]))
					$arItem["#"]["link"][0]["#"] = $arItem["#"]["link"][0]["#"]["cdata-section"][0]["#"];
				$arResult["item"][$i]["link"] = $arItem["#"]["link"][0]["#"];

				if ($arItem["#"]["enclosure"])
				{
					$arResult["item"][$i]["enclosure"]["url"] = $arItem["#"]["enclosure"][0]["@"]["url"];
					$arResult["item"][$i]["enclosure"]["length"] = $arItem["#"]["enclosure"][0]["@"]["length"];
					$arResult["item"][$i]["enclosure"]["type"] = $arItem["#"]["enclosure"][0]["@"]["type"];
					if ($arItem["#"]["enclosure"][0]["@"]["width"])
					{
						$arResult["item"][$i]["enclosure"]["width"] = $arItem["#"]["enclosure"][0]["@"]["width"];
					}
					if ($arItem["#"]["enclosure"][0]["@"]["height"])
					{
						$arResult["item"][$i]["enclosure"]["height"] = $arItem["#"]["enclosure"][0]["@"]["height"];
					}
				}
				$arResult["item"][$i]["category"] = $arItem["#"]["category"][0]["#"];
				$arResult["item"][$i]["pubDate"] = $arItem["#"]["pubDate"][0]["#"];

				$arRes["item"][$i] = $arItem;
			}
		}
		else
		{
			$arResult = array(
				"title" => $arRes["channel"][0]["#"]["title"][0]["#"],
				"link" => $arRes["channel"][0]["#"]["link"][0]["#"],
				"description" => $arRes["channel"][0]["#"]["description"][0]["#"],
				"lastBuildDate" => $arRes["channel"][0]["#"]["lastBuildDate"][0]["#"],
				"ttl" => $arRes["channel"][0]["#"]["ttl"][0]["#"],
			);

			if ($arRes["image"])
			{
				$arResult["image"]["title"] = $arRes["image"][0]["#"]["title"][0]["#"];
				$arResult["image"]["url"] = $arRes["image"][0]["#"]["url"][0]["#"];
				$arResult["image"]["link"] = $arRes["image"][0]["#"]["link"][0]["#"];
				$arResult["image"]["width"] = $arRes["image"][0]["#"]["width"][0]["#"];
				$arResult["image"]["height"] = $arRes["image"][0]["#"]["height"][0]["#"];
			}

			foreach($arRes["item"] as $i => $arItem)
			{
				if(!is_array($arItem) || !is_array($arItem["#"]))
					continue;

				if(is_array($arItem["#"]["title"][0]["#"]))
					$arItem["#"]["title"][0]["#"] = $arItem["#"]["title"][0]["#"]["cdata-section"][0]["#"];

				if(is_array($arItem["#"]["description"][0]["#"]))
					$arItem["#"]["description"][0]["#"] = $arItem["#"]["description"][0]["#"]["cdata-section"][0]["#"];
				elseif(is_array($arItem["#"]["encoded"][0]["#"]))
					$arItem["#"]["description"][0]["#"] = $arItem["#"]["encoded"][0]["#"]["cdata-section"][0]["#"];
				$arResult["item"][$i]["description"] = $arItem["#"]["description"][0]["#"];

				$arResult["item"][$i]["title"] = $arItem["#"]["title"][0]["#"];
				$arResult["item"][$i]["link"] = $arItem["#"]["link"][0]["#"];
				if ($arItem["#"]["enclosure"])
				{
					$arResult["item"][$i]["enclosure"]["url"] = $arItem["#"]["enclosure"][0]["@"]["url"];
					$arResult["item"][$i]["enclosure"]["length"] = $arItem["#"]["enclosure"][0]["@"]["length"];
					$arResult["item"][$i]["enclosure"]["type"] = $arItem["#"]["enclosure"][0]["@"]["type"];
					if ($arItem["#"]["enclosure"][0]["@"]["width"])
					{
						$arResult["item"][$i]["enclosure"]["width"] = $arItem["#"]["enclosure"][0]["@"]["width"];
					}
					if ($arItem["#"]["enclosure"][0]["@"]["height"])
					{
						$arResult["item"][$i]["enclosure"]["height"] = $arItem["#"]["enclosure"][0]["@"]["height"];
					}
				}
				$arResult["item"][$i]["category"] = $arItem["#"]["category"][0]["#"];
				$arResult["item"][$i]["pubDate"] = $arItem["#"]["pubDate"][0]["#"];

				$arRes["item"][$i] = $arItem;
			}
		}
		return $arResult;
	}

	function XMLDate2Dec($date_XML, $dateFormat = "DD.MM.YYYY")
	{
		static $MonthChar2Num = Array("","jan","feb","mar","apr","may","jun","jul","aug","sep","oct","nov","dec");

		if(preg_match("/(\\d+)\\s+(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)\\s+(\\d+)/i", $date_XML, $match))
			$timestamp = mktime(0, 0, 0, array_search(strtolower($match[2]), $MonthChar2Num), $match[1], $match[3]);
		else
			$timestamp = time();

		return  date(CDatabase::DateFormatToPHP($dateFormat), $timestamp);
	}

	function ExtractProperties($str, &$arProps, &$arItem)
	{
		reset($arProps);
		while (list($key, $val) = each($arProps))
			$str = str_replace("#".$key."#", $val["VALUE"], $str);
		reset($arItem);
		while (list($key, $val) = each($arItem))
			$str = str_replace("#".$key."#", $val, $str);
		return $str;
	}

	function GetRSS($ID, $LANG, $TYPE, $LIMIT_NUM = false, $LIMIT_DAY = false, $yandex = false)
	{
		echo "<"."?xml version=\"1.0\" encoding=\"".LANG_CHARSET."\"?".">\n";
		echo "<rss version=\"2.0\"";
		echo ">\n";

		$dbr = CIBlock::GetList(array(), array(
			"type" => $TYPE,
			"LID" => $LANG,
			"ACTIVE" => "Y",
			"ID" => $ID,
		));
		$arIBlock = $dbr->Fetch();
		if ($arIBlock && ($arIBlock["RSS_ACTIVE"] == "Y"))
		{
			echo CIBlockRSS::GetRSSText($arIBlock, $LIMIT_NUM, $LIMIT_DAY, $yandex);
		}

		echo "</rss>\n";
	}
}
?>