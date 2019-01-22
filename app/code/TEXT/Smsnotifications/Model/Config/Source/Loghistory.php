<?php
namespace TEXT\Smsnotifications\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use \Magento\Framework\Event\Observer       as Observer;
use \Magento\Framework\View\Element\Context as Context;
use \TEXT\Smsnotifications\Helper\Data      as Helper;
use Magento\Framework\App\Bootstrap;

/*
Custome class for multiselect order status value
*/
class Loghistory implements ArrayInterface
{
 public $resultPageFactory ;

  public function __construct(
      Context $context,
      Helper $helper,
      \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->_helper  = $helper;
        $this->_request = $context->getRequest();
        $this->_layout  = $context->getLayout();
        $this->resultPageFactory  = $resultPageFactory;
    }
  public function toOptionArray()
    {
        $settings = $this->_helper->getSettings();
        $Textl = new \TEXT\Smsnotifications\Observer\Textlocal(false, false, $settings['sms_auth_token'], false);
        $min_time = strtotime('-1 month');
        $max_time = time();
        $limit = 1000;
        $start = 0;

        $response = $Textl->getAPIMessageHistory($start, $limit, $min_time, $max_time);
       
        $messages = $response->messages;

         $jsonData = json_encode($messages);

        if (filter_input(INPUT_GET, 'export')) {
            require ('app/bootstrap.php');
            $params = $_SERVER;
            $bootstrap = Bootstrap::create(BP, $params);

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $directory = $objectManager->get('\Magento\Framework\Filesystem\DirectoryList');
            $rootPath  =  $directory->getRoot();
        
            $outputFile =  "loghistory_" . date('Y-m-d') . ".csv";
            $filepath =  $rootPath."/".$outputFile;
            $messages1 = array();
            foreach ($messages as $key => $value) {
                    $Datetime=$value->datetime;
                    $Number =$value->number;
                    $Sender= $value->sender;
                    $Message=$value->content;
                    $Status=$value->status;
                    $messages1[] = array(
                        'Datetime' =>$Datetime ,
                        'Number' => $Number,
                        'Sender' => $Sender,
                        'Message' => $Message,
                        'Status' => $Status
                            );
            }
            $write = fopen('php://output', 'w');
            $heading = false;
            if (!empty($messages1))
            foreach ($messages1 as $rows) {
                if (!$heading) {
              fputcsv($write, array_keys($rows));
                    $heading = true;
                }
                fputcsv($write, array_values($rows));
          }
  
            ob_start();
            fclose($write);
            header('Content-Type:application/octet-stream');
            header('Content-Disposition:filename='.$outputFile);
            header('Content-Length:' . filesize($filepath));
            unlink($filepath);
            ob_end_clean();
        }
    ?>

    <div class="lg_history" id="popup-mpdal" style="display:none">
    <div class="popop-model-inner-wrap">  <div class="close-popop"><span>+</span></div>
      
       <html>
<head>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twbs-pagination/1.4.1/jquery.twbsPagination.min.js"></script>
</head>
<body>

<table id="textlocal_log" class="table table-bordered table table-hover" cellspacing="0" width="100%">
     <colgroup><col><col><col></colgroup>
      <thead>
      <tr>
            <th width="20%">Datetime</th>
            <th width="20%">Number</th>
            <th width="20%">Sender</th>
            <th width="30%">Message</th>
            <th width="5%">Status</th>
      </tr>
      </thead>
      <tbody id="log_history">
      </tbody>
</table>
<div id="pager">
      <ul id="pagination" class="pagination-sm"></ul>
</div>
      <div class="exportlink">
      <a href='Loghistory.php?export=true'>ExportCSV</a>
      </div>

<script type="text/javascript">
  var data = <?=json_encode($messages)?>;
   var PerPagerec = 10;
  var RecordsTotal = data.length;
  var Pages = Math.ceil(RecordsTotal / PerPagerec);
  totalRecords = 0,
  recPerPage = 10,
  page = 1,

  jQuery('#pagination').twbsPagination({
         totalPages: Pages,
           visiblePages: 20,
        next: 'Next',
        prev: 'Prev',

  onPageClick: function (event, page, recored) {
                  records = data;
                 totalRecords = records.length;
                 totalPages = Math.ceil(totalRecords / recPerPage);
                     //console.log(totalRecords);
                  displayRecordsIndex = Math.max(page - 1, 0) * recPerPage;
                  endRec = (displayRecordsIndex) + recPerPage;
                 displayRecords = records.slice(displayRecordsIndex, endRec);
                  var tr;
              jQuery('#log_history').html('');
              for (var i = 0; i < displayRecords.length; i++) {
            tr = jQuery('<tr/>');
            tr.append("<td>" + displayRecords[i].datetime + "</td>");
            tr.append("<td>" + displayRecords[i].number + "</td>");
            tr.append("<td>" + displayRecords[i].sender + "</td>");
            tr.append("<td>" + displayRecords[i].content + "</td>");
            tr.append("<td>" + displayRecords[i].status + "</td>");
            jQuery('#log_history').append(tr);
      }
      }
 });
</script>
  </body>
      </div>
      </div>
<?php
    }
}