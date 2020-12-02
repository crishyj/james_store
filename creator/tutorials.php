<?php
        require 'header.php';
        require_once 'includes/functions.php';
		
		

        include 'menu.php';
        ?>
        <div id="app-container">
            <?php if  (isset($err)) { ?>
            <!-- Alert kan zijn: alert-info, alert-warning en alert-danger -->
            <div class="alert alert-danger alert-dismissable">
                 
                <h4>
                    Warning!
                </h4> <?php echo $err; ?>
            </div>
            <?php } ?>
            <div class="container">
                <div class="jumbotron col-md-4">
                
                    <h2><span class="glyphicon glyphicon-film"></span> Tutorials</h2>
                    <p>Learn how to use the ePub Creator Studio to create wonderful books.</p>

                </div>
                
                <div class="col-md-8 list-view" style="margin-top:50px;">


                    <h2>Tutorials</h2>
                    <hr>

                        <div class="col-xs-4 col-sm-3 col-md-2 library-item" style="height:170px;">
                            <div class="info-wrap">
                                <a href="https://emrepublishing.com/Movies/tutorial%201%20%28load%20template%29/tutorial%201%20%28load%20template%29.html" target="_blank" type="button" style="background: none; border: none; padding: 0; margin: 0;" >
                       
                                    <img aria-hidden="true" class="img-responsive" src="images/tut1.png" alt="" /> 
                       
                                </a>
                            </div>
                             <div class="caption book-info" style="margin-top:30px;">
                                    <h4 class="title">1: Using Templates</h4>
                                    <div class="author">Insert premade templates into your ebooks.</div>
									
									<div class="library-item" style="height:100px; margin-left:30px;">
										<div class="info-wrap">
											<a href="http://emrepublishing.com/books/V1-Templates.zip" target="_blank" type="button" style="background: none; border: none; padding: 0; margin: 0;" >
								   
												<img aria-hidden="true" class="img-responsive" style="height:40px;" src="images/templates.jpg" alt="" />
												 
											</a>
										</div>
										 <div class="caption book-info">
												<h4 class="title"><a href="http://emrepublishing.com/books/V1-Templates.zip" target="_blank" style="font-size:16px;" >1a: Free Templates</a></h4>
												<div class="author">Download this collection of eBook templates and fonts to use in your creations.</div>
										</div>
									</div>
                            </div>
                        </div>
                         
                        

                        
                        
                       
                   
                       
                       

                        <div class="col-xs-4 col-sm-3 col-md-2 library-item" style="height:100px;">
                            <div class="info-wrap">
                                <a href="https://emrepublishing.com/Movies/tutorial%202%20%28insert%20story%29/tutorial%202%20%28insert%20story%29.html" target="_blank" type="button" style="background: none; border: none; padding: 0; margin: 0;" >
                       
                                    <img aria-hidden="true" class="img-responsive" src="images/tut2.png" alt="" /> 
                       
                                </a>
                            </div>
                             <div class="caption book-info">
                                    <h4 class="title">2: Story Links</h4>
                                    <div class="author">Use interactive links to give the reader a choice.</div>
                            </div>
                        </div>

                         <div class="col-xs-4 col-sm-3 col-md-2 library-item" style="height:100px;">
                            <div class="info-wrap">
                                <a href="https://emrepublishing.com/Movies/tutorial%201%20(load%20template)/tutorial%201%20(load%20template).html" target="_blank" type="button" style="background: none; border: none; padding: 0; margin: 0;" >
                       
                                    <img aria-hidden="true" class="img-responsive" src="/Movies/tutorial%203%20(playlist)/tutorial_3_(playlist)_First_Frame.png" alt="" /> 
                       
                                </a>
                            </div>
                             <div class="caption book-info">
                                    <h4 class="title">3: Playlists</h4>
                                    <div class="author">Creating audio playlists with background music.</div>
                            </div>
                        </div>

                        <div class="col-xs-4 col-sm-3 col-md-2 library-item" style="height:100px;">
                            <div class="info-wrap">
                                <a href="https://emrepublishing.com/Movies/tutorial%204%20(embed%20youtube)/tutorial%204.html" target="_blank" type="button" style="background: none; border: none; padding: 0; margin: 0;" >
                       
                                    <img aria-hidden="true" class="img-responsive" src="/Movies/tutorial%203%20(playlist)/tutorial_3_(playlist)_First_Frame.png" alt="" /> 
                       
                                </a>
                            </div>
                             <div class="caption book-info">
                                    <h4 class="title">4: Embed Youtube</h4>
                                    <div class="author">How to embed videos from youtube and vimeo.</div>
                            </div>
                        </div>

                        <div class="col-xs-4 col-sm-3 col-md-2 library-item" style="height:100px;">
                            <div class="info-wrap">
                                <a href="https://emrepublishing.com/market-new-embellisher-ereader-app/" target="_blank" type="button" style="background: none; border: none; padding: 0; margin: 0;" >
                       
                                    <img aria-hidden="true" class="img-responsive" src="images/tut1.png" alt="" /> 
                       
                                </a>
                            </div>
                             <div class="caption book-info">
                                    <h4 class="title">5: Marketing</h4>
                                    <div class="author">View the following 5 parts:<br/>
                                        <a href="https://emrepublishing.com/Movies/tutorial%205%20(step%201)/promos%20tutorial%205%20(step%201).html" target="_blank" class="btn btn-default"  >Part 1<a/>
                                             <a href="https://emrepublishing.com/Movies/Tutorial%205%20(step%202)/Tutorial%205%20(step%202).html" target="_blank" class="btn btn-default"  >Part 2<a/>
                                                 <a href="https://emrepublishing.com/Movies/tutorial%205%20(step%203)/tutorial%205%20(step%203).html" target="_blank" class="btn btn-default"  >Part 3<a/>
                                                     <a href="https://emrepublishing.com/Movies/tutorial%205%20(step%204)/tutorial%205%20(step%204).html" target="_blank" class="btn btn-default"  >Part 4<a/>
                                                         <a href="https://emrepublishing.com/Movies/tutorial%205%20(step%205)/tutorial%205%20(step%205).html" target="_blank" class="btn btn-default"  >Part 5<a/>
                                                         <a href="https://emrepublishing.com/Movies/Tutorial%205%20(step%206)/Tutorial%205%20(step%206).html" target="_blank" class="btn btn-default"  >Part 6<a/>
                                                         <a href="https://emrepublishing.com/Movies/Tutorial%205%20(step%207)/Tutorial%205%20(step%207).html" target="_blank" class="btn btn-default"  >Part 7<a/>
                                                         <a href="https://emrepublishing.com/Movies/Sales/Sales.html" target="_blank" class="btn btn-default"  >Part 8 EMRE does it for you!<a/>
                                    </div>
                            </div>
                        </div>

                        


                        


                </div>
            </div>
        </div>
    </body>


</html>