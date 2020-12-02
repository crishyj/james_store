
<?php
include 'api/config.php';

    ?>

<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="images/embellisherereader_favicon.png" rel="shortcut icon"/>
    <link href="images/embellisherereader-touch-icon.png" rel="apple-touch-icon"/>
        

    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="css/viewer.css">
    <link rel="stylesheet" type="text/css" href="css/branding.css">
    <link rel="stylesheet" type="text/css" href="css/library.css">
    <link rel="stylesheet" type="text/css" href="css/readium_js.css">


    </head>

    <!-- This is all application-specific HTML -->
    <body style="overflow:auto;">
    <nav id="app-navbar" class="navbar" role="navigation">
    <div class="btn-group navbar-left">
            <!-- <button type="button" class="btn btn-default icon icon-show-hide"></button>  -->
        <button id="aboutButt1" tabindex="1" type="button" class="btn icon-logo" data-toggle="modal" data-target="#about-dialog" title="{{strings.about}}" aria-label="{{strings.about}}">
            <span class="icon-readium" aria-hidden="true"></span>
        </button>
        
       
    </div>
    <div class="btn-group navbar-right">
        
        <a  href="index.html" tabindex="1" type="button" class="btn icon-library" title="{{strings.view_library}} [{{keyboard.SwitchToLibrary}}]" aria-label="{{strings.view_library}}">
            <span class="glyphicon glyphicon-home" aria-hidden="true"></span>
        </a>

       
        <!-- <button type="button" class="btn btn-default icon icon-bookmark"></button>-->
    </div>
</nav>

        <div id="app-container">
            
            <div class="jumbotron">
                <div class="container">
                    <h1 style="font-size:28px;">All available books</h1>
                    <p>Here you can view all available books for the Embellisher Ereader app.</p>
                     
                </div>
            </div>

           

            <div class="col-md-12">
                <div id="tab-storeitems" class="tab-pane active">

                    <div class="panel panel-info">
                        <div class="panel-heading">
                         
                        </div>
                        <div class="panel-body">    
                            <table class="table table-striped">  
                                <thead>  
                                  <tr> 
                                    <th>Image</th>
                                    <th>Title</th> 
                                    <th>Author</th> 
                                    <th>Genre</th> 
                                    <th>Description</th> 
                                    <th>Price</th>  
                                    <th></th>
                                  </tr>  
                                </thead>  
                                <tbody>                    
                            <?php
                                $SQL = "SELECT * FROM library ORDER BY id";
                                $RES = $DB->query($SQL);
                                while ($epub = $RES->fetch_assoc()){
                                    echo '<tr>';  
                                    echo '<td><img src="'.$epub['coverHref'].'" alt="covericon" style="max-height:50px;"></td> ';
                                    echo '<td>'.$epub['title'].'</td> ';
                                    echo '<td>'.$epub['author'].'</td> ';
                                    echo '<td>'.$epub['genre'].'</td> ';
                                    echo '<td>'.$epub['description'].'</td> ';
                                    if ($epub['price'] != "" && $epub['price'] != "Free"){
                                        $epub['price'] = "$".(0+$epub['price']);
                                    }
                                    echo '<td>'.$epub['price'].'</td> ';
                                    echo '<td><a target="_blank" href="index.html?bookid='.$epub['id'].'" class="btn btn-default">Open in app</a></td>';
                                    echo '</tr> ';
                                }
                            ?>
                                </tbody> 
                            </table>
                            
                        </div>
                    </div>
                </div>
            </div>
                
            
        </div>
    </body>

</html>
