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

  if (isset($_POST['submit'])) {
      $fileUpload = $_FILES['image']['name'];
      $content = fopen($_FILES['image']['tmp_name'], 'r');
      $blobClient->createBlockBlob("imagesirvan", $fileUpload, $content);
  }

  $listBlobsOptions = new ListBlobsOptions();
  $listBlobsOptions->setPrefix("");
  $result = $blobClient->listBlobs("imagesirvan", $listBlobsOptions);

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
  <h1 class="display-4">Image Vision</h1>
  
</div>

<div class="container">
  <div class="card-deck mb-3 text-center">
      <div class="col-md-6 inihide" style="display: none;">
        <div id="imageDiv" style="width:420px; display:table-cell;">
          Source image:
          <br><br>
          <img id="sourceImage" width="400" />
        </div>
      </div>
      <div class="col-md-6 inihide" style="display: none;">
        <div id="jsonOutput" style="width:600px; display:table-cell;">
          Response:
          <br><br>
          <textarea id="responseTextArea" class="UIInput"
                    style="width:580px; height:400px;"></textarea>
       </div>
      </div>
      <div class="col-md-12">
      <form method="post" action="index.php" enctype="multipart/form-data">
      <div class="form-group row">
        <label for="inputImages" class="col-sm-2 col-form-label">Images</label>
          <div class="col-sm-10">
            <input type="file" name="image" class="form-control" id="inputImages">
          </div>
        </div>
        <div class="form-group row">
          <div class="col-sm-3">
            <button type="submit" name="submit" class="btn btn-primary">Upload</button>
          </div>
        </div>
      </form>

      </div>

    <div class="col-md-12">
    <table class="table">
    <thead class="thead-dark">
      <tr>
        <th scope="col">No</th>
        <th scope="col">Nama File</th>
        <th scope="col">Link</th>
        <th scope="col">Aksi</th>
      </tr>
    </thead>
    <tbody>
    <?php
      $i = 0;
      do {
        foreach($result->getBlobs() as $data) {
          $i++;
    ?>
    <tr>
          <td><?=$i?></td>
          <td><?= $data->getName() ?></td>
          <td><?= $data->getUrl() ?></td>
          <td>
            <button type="button" name="analyze"
             img="<?= $data->getUrl() ?>" class="btn btn-primary vision">Analyze</button>
          </td>
    </tr>
    <?php
      }
        $listBlobsOptions->setContinuationToken($result->getContinuationToken());
      }while($result->getContinuationToken());
    ?>
    </tbody>
</table>

    </div>
  </div>

  <footer class="pt-4 my-md-5 pt-md-5 border-top">
    <div class="row">
      <div class="col-12 col-md">
      
        <small class="d-block mb-3 text-muted">&copy; 2019 Irvan Lutfi Gunawan</small>
      </div>
    </div>
  </footer>
</div>



<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script type="text/javascript">
  $('.vision').click(function() {
        $('.inihide').show();
        var subscriptionKey = "99b3562b1b40436cafe313d20e42412f";
 
        var uriBase =
            "https://southeastasia.api.cognitive.microsoft.com/vision/v2.0/analyze";
 
        // Request parameters.
        var params = {
            "visualFeatures": "Categories,Description,Color",
            "details": "",
            "language": "en",
        };
 
        // Display the image.
        var sourceImageUrl = $(this).attr('img');
        document.querySelector("#sourceImage").src = sourceImageUrl;
 
        // Make the REST API call.
        $.ajax({
            url: uriBase + "?" + $.param(params),
 
            // Request headers.
            beforeSend: function(xhrObj){
                xhrObj.setRequestHeader("Content-Type","application/json");
                xhrObj.setRequestHeader(
                    "Ocp-Apim-Subscription-Key", subscriptionKey);
            },
 
            type: "POST",
 
            // Request body.
            data: '{"url": ' + '"' + sourceImageUrl + '"}',
        })
 
        .done(function(data) {
            // Show formatted JSON on webpage.
            $("#responseTextArea").val(JSON.stringify(data, null, 2));
        })
 
        .fail(function(jqXHR, textStatus, errorThrown) {
            // Display error message.
            var errorString = (errorThrown === "") ? "Error. " :
                errorThrown + " (" + jqXHR.status + "): ";
            errorString += (jqXHR.responseText === "") ? "" :
                jQuery.parseJSON(jqXHR.responseText).message;
            alert(errorString);
        });
  })
</script>

</body>
</html>