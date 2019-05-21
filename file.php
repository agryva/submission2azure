<?php

require_once 'vendor/autoload.php';
require_once "./random_string.php";

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

$connectionString = "DefaultEndpointsProtocol=https;AccountName=dicodingsubmission2;AccountKey=//FdvH3Zc1ETRENs5DJGJcC4bztfkFaeOfnrhHxJ8ofMYLSKiHzI0605X4xHeeQ82z8qHsHeQ8cGIi4+VjmZhw==;EndpointSuffix=core.windows.net";

// Create blob client.
$blobClient = BlobRestProxy::createBlobService($connectionString);


$fileToUpload = "fileupload.txt";
$dataBlob = null;



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Submission 2</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</head>
<body>
<div class="d-flex flex-column flex-md-row align-items-center p-3 px-md-4 mb-3 bg-white border-bottom shadow-sm">
  <h5 class="my-0 mr-md-auto font-weight-normal">Azure Submission 2</h5>
  <nav class="my-2 my-md-0 mr-md-3">
    <a class="p-2 text-dark" href="index.php">Image Vision</a>
    <a class="p-2 text-dark" href="file.php">File</a>
  </nav>
</div>

<div class="pricing-header px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
  <h1 class="display-4">Azure Storage</h1>
  <form method="post" action="file.php" class="align-center text-center">
        <button type="submit" name="upload">Press to Upload File</button>
    </form>
</div>

<div class="container">
  <div class="card-deck mb-3 text-center">
   
    <br>
    <?php
        if (isset($_POST["upload"])) {
            if (isset($_POST["upload"])) {
                // Create container options object.
                $createContainerOptions = new CreateContainerOptions();
            
                $createContainerOptions->setPublicAccess(PublicAccessType::CONTAINER_AND_BLOBS);
            
                // Set container metadata.
                $createContainerOptions->addMetaData("key1", "value1");
                $createContainerOptions->addMetaData("key2", "value2");
            
                  $containerName = "blockblobs".generateRandomString();
            
                try {
                    $blobClient->createContainer($containerName, $createContainerOptions);
                    $myfile = fopen($fileToUpload, "r") or die("Unable to open file!");
                    fclose($myfile);
                    echo "<div class='text-center'>";
                    echo "Uploading BlockBlob: ".PHP_EOL;
                    echo " ".$fileToUpload;
                    echo "<br />";
                    echo "</div>";
            
                    $content = fopen($fileToUpload, "r");
            
                    //Upload blob
                    $blobClient->createBlockBlob($containerName, $fileToUpload, $content);
            
                    // List blobs.
                    $listBlobsOptions = new ListBlobsOptions();
                    $listBlobsOptions->setPrefix("HelloWorld");
            
                    do{
                        $result = $blobClient->listBlobs($containerName, $listBlobsOptions);
                        foreach ($result->getBlobs() as $blob)
                        {
                            echo $blob->getName().": ".$blob->getUrl()."<br />";
                        }
            
                        $listBlobsOptions->setContinuationToken($result->getContinuationToken());
                    } while($result->getContinuationToken());
                    echo "<br />";
            
                    // Get blob.
                    $blob = $blobClient->getBlob($containerName, $fileToUpload);
                    $dataBlob = $blob->getContentStream();
                    echo "<br /> <p class='text-center'>";
                    fpassthru($blob->getContentStream());
                    echo "<br />";
                    echo "</p>";
                }
                catch(ServiceException $e){
                    // Handle exception based on error codes and messages.
                    // Error codes and messages are here:
                    // http://msdn.microsoft.com/library/azure/dd179439.aspx
                    $code = $e->getCode();
                    $error_message = $e->getMessage();
                    echo $code.": ".$error_message."<br />";
                }
                catch(InvalidArgumentTypeException $e){
                    // Handle exception based on error codes and messages.
                    // Error codes and messages are here:
                    // http://msdn.microsoft.com/library/azure/dd179439.aspx
                    $code = $e->getCode();
                    $error_message = $e->getMessage();
                    echo $code.": ".$error_message."<br />";
                }
            }
        }

    ?>
  </div>

  <footer class="pt-4 my-md-5 pt-md-5 border-top">
    <div class="row">
      <div class="col-12 col-md">
        <img class="mb-2" src="/docs/4.3/assets/brand/bootstrap-solid.svg" alt="" width="24" height="24">
        <small class="d-block mb-3 text-muted">&copy; 2019 Irvan Lutfi Gunawan</small>
      </div>
    </div>
  </footer>
</div>



<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>

</body>
</html>