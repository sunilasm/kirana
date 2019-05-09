<?php

namespace Retailinsights\Slider\Controller\Adminhtml\Index;

use \Magento\Store\Model\StoreManagerInterface;


class Index extends \Magento\Backend\App\Action
{
    protected $_storeManager;
    protected $resultPageFactory;
    protected $_postFactory;
    protected $_messageManager;
    
    public function __construct(
                \Magento\Backend\App\Action\Context $context,
                StoreManagerInterface $storeManager,
                \Magento\Framework\View\Result\PageFactory $resultPageFactory,
                \Retailinsights\Slider\Model\PostFactory $PostFactory,
                \Magento\Framework\Message\ManagerInterface $messageManager
    ){
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_storeManager = $storeManager;
        $this->_postFactory = $PostFactory;
        $this->_messageManager = $messageManager;
    }
    public function execute()
    { 
        if(isset($_POST["submit"]) && !empty($_POST["submit"]))
        { 
           $selected_val = $_POST['select_values'];
            if($selected_val == "no") { 
              $this->messageManager->addError(__('Choose a promotion type'));
              $this->_redirect('slider/index/index');
              return false;
            }else{
            $check = true;
            $target_dir = "pub/media/uploads/";
            $selected_val = $_POST['select_values'];
            
            //echo $selected_val;
            $img_name = rand()."_".$_FILES["img_upload"]["name"];
          
            $target_file = $target_dir . basename($img_name);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
            
            if(isset($_POST["submit"])) {

              if(!empty($_FILES["img_upload"]["tmp_name"])){
                $check = getimagesize($_FILES["img_upload"]["tmp_name"]);
              }
             
              if($check !== false) {
                  // echo "File is an image - " . $check["mime"] . ".";
                  $uploadOk = 1;
              } else {
                  //echo "File is not an image.";
                  $this->messageManager->addError(__('File is not an image.'));
                  $this->_redirect('slider/index/index');
                  $uploadOk = 0;
              }
            }
        }
          // Check if file already exists
          if (file_exists($target_file)) {
              // echo "Sorry, file already exists.";
              $this->messageManager->addError(__('Sorry, file already exists.'));
              $this->_redirect('slider/index/index');
              $uploadOk = 0;
          }
         // print_r($_FILES); exit;

          // // Check file size
          // if ($_FILES["img_upload"]["size"] > 500000 && $_FILES["china_upload"]["size"] > 500000 && $_FILES["india_upload"]["size"] > 500000) {
          //     echo "Sorry, your file is too large.";
          //     $uploadOk = 0;
          // }

          // Allow certain file formats
          // if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" && $imageFileType_china != "jpg" && $imageFileType_china != "png" && $imageFileType_china != "jpeg" && $imageFileType_china != "gif" && $check_india != "jpg" && $check_india != "png" && $check_india != "jpeg" && $check_india != "gif" ) {
          //     //echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
          //     $this->messageManager->addError(__('Sorry, only JPG, JPEG, PNG & GIF files are allowed.'));
          //     $this->_redirect('slider/index/index');
          //     $uploadOk = 0;
          // }

          // Check if $uploadOk is set to 0 by an error
          if ($uploadOk == 0) {
              //echo "Sorry, your file was not uploaded.";
              $this->messageManager->addError(__('Sorry, your file was not uploaded.'));
              $this->_redirect('slider/index/index');
          // if everything is ok, try to upload file
          } else {
              $base_url = $this->_storeManager->getStore()->getBaseUrl(); 
              $old_name = $_FILES["img_upload"]["tmp_name"];
              
            // $new_name = rename($base_url.''.$old_name , $base_url.''.$selected_val.'_1');
        

              $case = move_uploaded_file($old_name, $target_file);
                
              if ($case) {
                  //save it in the table
                  try {
                    if(!empty($_FILES["img_upload"]["tmp_name"]))
                    {
                      $this->_postFactory->create()->setData(
                      array(
                        'promo_type' => $selected_val,
                        'image_path' => $base_url.''.$target_file
                      ))->save(); 
                    }

                    $this->messageManager->addSuccess(__('Image uploaded successfully'));

                    //code_1
           
                      $this->_redirect('slider/index/index');
                    } catch (Exception $e) {
                      $this->messageManager->addError($e->getMessage());
                      $this->_redirect('slider/index/index');
                    }
                    //ends here
                } else {
                    //echo "Sorry, there was an error uploading your file.";
                    $this->messageManager->addError(__('Sorry, there was an error uploading your file.'));
                    $this->_redirect('slider/index/index');
                }
            }


        }
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
	}
}

?>