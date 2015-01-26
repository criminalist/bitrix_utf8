<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Тех.обслуживание ККТ");
?> Техническое обслуживание делится на два типа: планово-профилактические работы и выезд по требованию. . Профилактика неисправностей ККТ очень важна для ее бесперебойной работы и предотвращения поломок в будущем. В случае поломки ККТ владелец может получить консультацию по телефону либо вызвать специалиста для устранения неисправности. Если невозможно починить ККТ на месте, то ее забирают для ремонта в ЦТО.
<br />
 
<br />
 Обычно стоимость технического обслуживания фиксируется в виде абонентской платы за квартал, не зависящей от количества выездов специалиста. 
<br />
 
<br />
 <b>Составляющие технического обслуживания:</b> 
<br />
 
<br />
 &bull; проводится внешний осмотр ККТ, удаляются загрязнения и устаревшие марки-пломбы 
<br />
 
<br />
 &bull; выявляется изношенность механических узлов 
<br />
 
<br />
 &bull; проводится  тестирование кассового аппарата 
<br />
 
<br />
 &bull; проводится проверка качества, полноты и правильности отображения обязательных реквизитов на чеке
<br />
 
<br />
 &bull; проводится проверка обязательных настроек кассового аппарата (дата, время, заводской номер) 
<br />
 
<br />
 &bull; настраиваются либо проверяются необязательные опции (начисление налогов, наличие и названия отделов в кассе, программирование безналичной секции, программирование шрифта, межстрочного интервала, опции печати суточных отчетов с гашением (Z-отчетов), рекламного текста, пароля кассира, ограничений по разрядности вводимых сумм) 
<br />
 
<br />
 &bull; опломбируется кассовый аппарат, и вносится запись в Журнал учета вызовов технических специалистов (форма КМ-8) о проведенном техническом обслуживании. 
<br />
 <i></i> 
<br />
Дополнительно ежегодно:
<br />
 
<br />
 &bull; вскрывается корпус с целью осмотра, и проверяются электронные блоки и узлы на их соответствие документации завода изготовителя 
<br />
 
<br />
 &bull; проверяются версии установленного драйвера кассового аппарата, проводится установка голограммы Сервисного обслуживания на текущий год. 
<div> 
  <h2>ОТПРАВИТЬ ЗАЯВКУ  
    <br />
   </h2>
 
  <br />
 <?$APPLICATION->IncludeComponent(
	"bitrix:main.feedback",
	"",
	Array(
	)
);?></div>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>