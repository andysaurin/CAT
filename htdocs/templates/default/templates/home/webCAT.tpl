
{if $smarty.session.errors}

					<div class="alert alert-danger">
						<button type="button" class="close" data-dismiss="alert">
							<i class="icon-remove"></i>
						</button>

						{$smarty.session.errors}

						<p>Correct these errors and try submitting again.</p>
						<br />
					</div>
{elseif $smarty.session.success}
					<div class="alert alert-success">
						<button type="button" class="close" data-dismiss="alert">
							<i class="icon-remove"></i>
						</button>

						{$smarty.session.success}

						<br />
					</div>
{/if}

					<!-- main -->
					<div style="padding:20px;">

						<!-- Main hero unit for a primary marketing message or call to action -->
						<div class="page-header">
							<h1 class="text">webCAT
								<small>
									<i class="icon-double-angle-right"></i>
									A web <b>C</b>hIP <b>A</b>ssociation <b>T</b>ester (webCAT)
								</small>
							</h1>

							<span class="row">
								<p>
									webCAT allows you to easily compare either
									<ul>
										<li>multiple ChIP peaks from peak-called  <a href="http://genome.ucsc.edu/FAQ/FAQformat.html" target="_blank">BED files</a> in a systematic "ChIP versus ChIP" approach.</li>
										<li>multiple ChIP peaks with defined genomic regions (e.g. introns, exons, promoters, intergenic regions, etc...)</li>
									</ul>
									To see whether there is any statistically significant association of "colocalisation".
									<br />
								</p>
								<p>
									Comparison is performed using the <a href="http://www.ncbi.nlm.nih.gov/pubmed/23782611" title="GAT" target="_blank">Genomic Association Tester</a> framework.
									<br />Scores are calculated as: Log<sub>2</sub>( observed overlap / expected overlap ) x -Log<sub>10</sub>( <i>p</i>-value )
								</p>
								<p>
									Peaks that share significantly common genomic binding sites score highly, allowing easy identification of proteins that show similar or identical genomic distributions from those that are distinct.
								</p>
							</span>

							<span class="row">

									<h4 class="text blue smaller">High genomic association or "co-localisation" between pairs of ChIP data can highlight potential :</h4>
									<ul class="list-unstyled spaced2">
										<li>
											&nbsp;&nbsp;<i class="icon-circle green"></i>
											&nbsp;&nbsp;functional complexes or co-factors acting on similar genes.
										</li>
										<li>
											&nbsp;&nbsp;<i class="icon-circle green"></i>
											&nbsp;&nbsp;antagonistic activators/repressors acting on similar genes.
										</li>
										<li>
											&nbsp;&nbsp;<i class="icon-circle green"></i>
											&nbsp;&nbsp;preference for a given chromatin-acting protein with one or more histone marks.
										</li>
										<li>
											&nbsp;&nbsp;<i class="icon-circle green"></i>
											&nbsp;&nbsp;co-occurring histone marks
										</li>
									</ul>


							</span>

{*
							<div class="row">
								<h4 class="text blue lead">To use GAT</h4>
								<ol>
									<li><strong>Select below the tissue(s) of interest.</strong></li>
									<li><strong>Datasets of ChIP'd proteins with pre-computed data available for these tissues can then be selected for analysis.</strong></li>
									<li><strong>Finally, you can simply obtain the association data selected, or upload your own BED file.</strong></li>
								</ol>
							</div>
*}
							<div class="row">
								<div class="col-xs-12 col-sm-10">
									<br />
									<div class="lead">A note on user-supplied BED files...</div>
									<div>
										<ul>
											<li>
												Your BED file <b>must</b> be <a href="http://genome.ucsc.edu/FAQ/FAQformat.html" target="_blank">correctly formatted</a> and the chromosome names be correct for the selected genome - different genome assemblies report chromosome names differently e.g. '1' instead of 'chr1'.
											</li>
	{*
											<li>
												The supplied BED file is usually a ChIP peak BED file which will compare "all ChIP peaks with all ChIP peaks".
												<br />However, you may wish to test association of ChIP peaks with individual genomic regions (e.g. genes).

												<ul>
													<li>
														If this is the case, you should check the option "Individual Genomic Regions", which will compare all the selected modENCODE ChIP peaks with each individual genomic region in the provided BED file.
													</li>
												</ul>
											</li>
	*}
											<li>
												Due to the time involved in calculating peak association, uploading your own ChIP peak BED file <b>requires</b> the input of a valid email address to which the results will be sent.
												<br />To give an example of execution time, each "calculation" takes between 1-4 seconds (depending on server load):
												<ul>
													<li>
														For "ChIP on genomic regions", it takes approximately 3 minutes to score {$max_chip_bedfiles} ChIP datasets against {$max_gene_regions} genomic regions ({$max_chip_bedfiles} calculations).
													</li>
													<li>
														For "ChIP versus ChIP", it takes approximately 2 hours for comparison of {$max_chip_bedfiles} uploaded ChIP BED files ({$max_chip_bedfiles * $max_chip_bedfiles} calculations).
													</li>
												</ul>
											</li>
											<li>
												User-supplied Genomic Region BED files are limited to {$max_gene_regions} unique region names, as given in the 4th (name) column of the BED file.
																							</li>
											<li>
												User-supplied ChIP peak BED files are limited to {$max_chip_bedfiles} ChIP peak BED files (unlimited regions){* , although if you require individual region testing (e.g. a gene BED file), then only 1 BED file is permitted, with a maximum of {$max_gene_regions} regions *}.
											</li>
										</ul>
									</div>
								</div>
							</div>

						</div> <!-- /.page-header -->

{if $smarty.session.load_alert}
					<div class="alert alert-danger">
						<button type="button" class="close" data-dismiss="alert">
							<i class="icon-remove"></i>
						</button>

						{$smarty.session.load_alert}

						<br />
					</div>

{elseif $smarty.session.load_warnings}
					<div class="alert alert-warning">
						<button type="button" class="close" data-dismiss="alert">
							<i class="icon-remove"></i>
						</button>

						{$smarty.session.load_warnings}

						<br />
					</div>

{/if}
						<!-- Main Data Entry -->

							<div class="widget-box">
								<div class="widget-header widget-header-blue widget-header-flat">
									<h2 class="blue">Let's get started!</h2>
								</div>

									<form class="form-horizontal" role="form" name="data_form" id="data_form" method="post" enctype="multipart/form-data">
										<input type=hidden name="userBedSubmit" value=1 />


								<!-- Main Data Entry Widget Body -->
								<div class="widget-body span12">



											<div class="form-group">

												<div class="space-12">&nbsp;</div>

												<label class="col-sm-4 control-label right" for="title"> Title</label>

												<div class="input-group col-xs-10 col-sm-4 no-padding-left">
													<input name="title" class="form-control" type="text" id="title" placeholder="Give a title to the run" value="{$smarty.request.title}" />
												</div>

											</div>

											<div class="space-4"></div>

											<div class="form-group">

												<label class="col-sm-4 control-label right" for="emailAddress">Email address</label>

												<div class="input-group col-xs-10 col-sm-4 no-padding-left has-error" id="emailAddressDiv">
													<span class="input-group-addon">
														<i class="icon-envelope"></i>
													</span>

													<input class="form-control input-mask-envelope" type="text" name="emailAddress" autocomplete="off" id="emailAddress" placeholder="Email address to send results to"  value="{$smarty.request.emailAddress}" />

												</div>

											</div>

											<div class="space-4"></div>

											<div class="form-group">

												<label class="col-sm-4 control-label right"> Type of analysis to perform?</label>

												<div class="btn-group no-padding-left">

													<span class="btn-toolbar inline middle no-margin">
														<span id="analysisType" data-toggle="buttons" class="btn-group no-margin">
															<label id="geneChIP" class="btn" data-rel="tooltip" title="Choose this to see for example association of ChIP peaks with specific genomic regions (promoters, introns, exons etc)" data-placement="bottom">
																ChIP on Genomic Regions
																<input class="analysisType" name="analysisType" type="radio" value="genechip" />
															</label>

															<label id="chipChIP" class="btn" data-rel="tooltip" title="Choose this to see for example colocalisation of ChIP peaks" data-placement="bottom">
																ChIP versus ChIP
																<input class="analysisType" name="analysisType" type="radio" value="chipchip" />
															</label>
														</span>
													</span>




{*
													<label id="geneChIP" data-toggle="buttons" class="btn btn-info" data-rel="tooltip" title="Choose this to see for example association of ChIP peaks with specific genomic regions (promoters, introns, exons etc)" data-placement="bottom">
														ChIP on Genomic Regions
														<input type="radio" value="1" />
													</label>
													<label id="chipChIP" class="btn btn-info" data-rel="tooltip" title="Choose this to see for example colocalisation of ChIP peaks" data-placement="bottom">ChIP versus ChIP</label>
*}
{* 													<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="More details." title="Popover on hover">?</span> *}

												</div>

											</div>


{*
																		<span class="btn-toolbar inline middle no-margin">
																			<span id="chosen-multiple-style" data-toggle="buttons" class="btn-group no-margin">
																				<label class="btn btn-xs btn-yellow active">
																					1
																					<input type="radio" value="1" />
																				</label>

																				<label class="btn btn-xs btn-yellow">
																					2
																					<input type="radio" value="2" />
																				</label>
																			</span>
																		</span>
*}
{*
					$('#chosen-multiple-style').on('click', function(e){
						var target = $(e.target).find('input[type=radio]');
						var which = parseInt(target.val());
						if(which == 'geneChIP') {
							$('#geneChIP').removeClass('btn-yellow');
							$('#geneChIP').addClass('btn-green');


							$('#form-field-select-4').addClass('tag-input-style');

						} else {
							$('#form-field-select-4').removeClass('tag-input-style');
						}
					});
*}

											<!-- remaining Parameters - Hide until analysisType has been selected -->
											<div id="remainingParams" style="display:none">

												<div class="space-4"></div>

												<div class="form-group" id="genome">

													<label class="col-sm-4 control-label right" for="genomeSelect">
														Select a genome
														<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="Select the genome on which the BED files are based. This is important for the chromosome sizes of the genome to determine statistical significance in overlaps." title="Which genome?">?</span>
													</label>

													<div class="form-group col-xs-10 col-sm-4">
														<select class="width-80" id="genomeSelect" name="genome" data-placeholder="Choose a genome...">

															<option value="">Select a genome</option>
															{foreach from=$genomes item=genome}
															<option value="{$genome}"{if !$smarty.request.genome && $genome == 'mm9'} selected{elseif $smarty.request.genome == $genomes} selected{/if}>{$genome}</option>
															{/foreach}

														</select>
													</div>

												</div>

	{*
													<div id="GeneBedFiles" style="display:none">
														<div class="form-group row">

															<div class="col-xs-4 input-group  form-group bedFile_IDs has-status" id="geneBedFile_File_ID">
																<span class="block input-icon input-icon-left">
																		<input name="GeneBedFiles[file_id][1]" type="text" class="input-small" id="geneBedFile_File_ID_Txt" placeholder="Expt. ID" data-rel="popover" data-trigger="hover" data-placement="top" title="Enter an experimental identifier for this dataset." data-content="This ID will be used as the column/row header in the resulting data matrix." />
																	<i class="icon-info-sign"></i>
																</span>
															</div>

															<div class="col-xs-8 input-group  form-group" id="geneBedFile_File_Div" style="display:inline">
																<input name="GeneBedFiles[file][1]" type="file" class="input_bedFile" id="geneBedFile_File" />
															</div>

														</div>
													</div><!-- /#GeneBedFiles -->
	*}

												<div class="space-4"></div>

												<div class="form-group" id="increase_sensitivity_div">

													<label class="col-sm-4 control-label right" for="increase_sensitivity"> <a href="/CAT/#ChIP_Association_Score">Score for:</a>
														<span  class="help-button param-help" data-rel="popover" data-trigger="hover" data-placement="left" data-html="true" title="" data-content="<p>Scoring for <b>overlapping peaks</b><br>&nbsp;&nbsp;&nbsp;&nbsp;will highlight pairwise peaks showing significant overlap (e.g. factors associating with histone marks).</p>Scoring for <b>highly similar peaks</b><br>&nbsp;&nbsp;&nbsp;&nbsp;will show significant overlap where each peak's start/stop coordinates are highly similar (e.g. transcription co-factors).</p>">?</span>
													</label>
													<div class="form-group col-xs-10 col-sm-4">
														<select class="col-xs-12" name="increase_sensitivity" class="form-control no-padding-right" id="increase_sensitivity">
															<option value="on">Highly similar peaks</option>
															<option value="off">Overlapping Peaks</option>
														</select>
													</div>

												</div>


												<div class="space-4"></div>

												<div class="form-group" id="GeneBedFile">

													<label class="col-sm-4 control-label right" for="GeneBedFile">
														Select Genomic Region BED file
														<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="The 4th (name) column of the BED file will determine how the genomic regions are grouped. e.g. if there are 10 unique name identifiers in all rows, then the webCAT output will have 10 rows, one for each unique name identifier. **NOTE** The genomic region file must contain no more than {$max_gene_regions} unique name identifiers." title="Formatting the &quot;Genomic Region&quot; BED file">?</span>
													</label>

														<div class="input-group col-xs-10 col-sm-4 no-padding-left">
															<input name="GeneBedFile" type="file" class="input_bedFile" id="geneBedFile_File" />
														</div>

												</div>


												<div class="space-4"></div>

												<div class="form-group">
													<label class="col-sm-4 control-label right" for="bedFileNumber"> Number of ChIP BED files to submit? </label>

													<div class="input-group" id="numberBedFiles">
														<input type="text" class="input-mini" id="bedFileNumber" style="min-width:45px;" readonly="" />
													</div>

												</div>

												<div class="form-group" id="ChipBedFiles">

													{for $i=1 to $min_chip_bedfiles}
													<div id="bedFile{$i}_File_Div">

														<div class="col-sm-4 control-label right" for="ChIPBedFile_File" style="margin:0;padding:0 5px 0 0;">
															<span class="input-group input-icon input-icon-left">
																<input name="ChipBedFiles[file_id][{$i}]" type="text" class="input-small" id="bedFile{$i}_File_ID_Txt" placeholder="Expt. ID"{if $i==1} data-rel="popover" data-trigger="hover" data-placement="top" title="Enter an experimental identifier for this dataset." data-content="This ID will be used as the column/row header in the resulting data matrix."{/if} />
																{if $i==1}<i class="icon-info-sign" data-rel="popover" data-trigger="hover" data-placement="top" title="Enter an experimental identifier for this dataset." data-content="This ID will be used as the column/row header in the resulting data matrix."></i>{/if}
															</span>
														</div>

														<div class="input-group col-xs-10 col-sm-4 no-padding-left">
															<input name="ChipBedFiles[file][{$i}]" type="file" class="input_bedFile" id="bedFile{$i}_File" />
														</div>

													</div>

													{/for}
													{for $i=($min_chip_bedfiles+1) to $max_chip_bedfiles}

													<div id="bedFile{$i}_File_Div" style="display:none;">

														<div class="col-sm-4 control-label right" for="ChIPBedFile_File" style="margin:0;padding:0 5px 0 0;">
															<span class="input-group input-icon input-icon-left">
																<input name="ChipBedFiles[file_id][{$i}]" type="text" class="input-small" id="bedFile{$i}_File_ID_Txt" placeholder="Expt. ID" />

															</span>
														</div>

														<div class="input-group col-xs-10 col-sm-4 no-padding-left">
															<input name="ChipBedFiles[file][{$i}]" type="file" class="input_bedFile" id="bedFile{$i}_File" />
														</div>

													</div>


													{/for}
												</div>

												<!-- /bedConfig -->



												<div class="space-4"></div>

												<div class="form-group" id="selfReporting">

													<label class="col-sm-4 control-label right" for="selfReport">
														Score "Self" <i>vs</i> "Self" as

														<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" title="How to report Self vs Self score in data matrix?" data-content='In analyses of an "all vs all" data matrix, how do you wish to represent the value of "self vs self"? Using the actual score may skew the analysis since the value for "self vs self" in terms of genomic association will always be the highest score for that factor. Note: this can be ignored if you are providing an "Individual Genomic Regions" BED file, since the resulting data is not an "all vs all" data matrix.'>?</span>

													</label>

													<div class="input-group col-xs-10 col-sm-4 no-padding-left input-small">
														<select name="selfReport" class="form-control no-padding-right" id="selfReport">
															<option value="NA" selected>NA</option>
															<option value="0">0</option>
															<option value="score">Actual Score</option>
														</select>
													</div>


												</div>



	{*


												<div class="form-group row">
													<label class="col-xs-8 control-label" for="selfReport">Score "Self" <i>vs</i> "Self" as</label>
													<div class="col-xs-3">
														<select name="selfReport" class="form-control no-padding-right" id="selfReport" disabled>
															<option value="NA" selected>NA</option>
															<option value="0">0</option>
															<option value="score">Actual Score</option>
														</select>
													</div>
													<div class="col-xs-1 no-padding-left">
														<span class="help-button param-help" style="display:none" data-rel="popover" data-trigger="hover" data-placement="left" title="How to report Self vs Self score in data matrix?" data-content='In analyses of an "all vs all" data matrix, how do you wish to represent the value of "self vs self"? Using the actual score may skew the analysis since the value for "self vs self" in terms of genomic association will always be the highest score for that factor. Note: this can be ignored if you are providing an "Individual Genomic Regions" BED file, since the resulting data is not an "all vs all" data matrix.'>?</span>
													</div>
												</div>
	*}

												<div class="form-group row" id="formSubmit">
													<div class="col-md-offset-3 col-md-9">
														<button type="submit" class="btn btn-sm" name="formSubmitBtn" id="formSubmitBtn">
															Calculate Data
															<i class="icon-arrow-right icon-on-right bigger-110"></i>
														</button>
													</div>

												</div>

											</div>
											<!-- End remaining Parameters -->

										</form>

									</div>
									<!-- //Select Items row -->

								</div>
								<!-- //Main Data Entry Widget Body -->

							</div><!-- //widget-box -->

						</div>
						<!-- End Main Data Entry /row-fluid -->
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>

					<!-- //main -->


					<script src="/assets/js/jquery.autosize.min.js"></script>
					<script src="/assets/js/jquery-ui-1.10.3.full.min.js"></script>
					<script src="/assets/js/jquery.ui.touch-punch.min.js"></script>

					<script src="/assets/js/fuelux/fuelux.spinner.min.js"></script>{* the "number of bedfiles number picker *}
					<script src="/assets/js/bootstrap-tag.min.js"></script>

					<!-- inline scripts related to this page -->

					<script type="text/javascript">
			{literal}
						jQuery( document ).ready( function($) {
			{/literal}
			{if !$class}
							var ajaxURL = "/{$module}/index/ajax?presenter=none";
			{else}
							var ajaxURL = "/{$module}/{$class}/ajax?presenter=none";
			{/if}
			{literal}
							$('[data-rel=popover]').popover({container:'body'});
							$('[data-rel=tooltip]').tooltip({container:'body'});


							//Tag control for omitting datasets
							//we could just set the data-provide="tag" of the element inside HTML, but IE8 fails!
							var tag_input = $('#form-field-tags');
							if(! ( /msie\s*(8|7|6)/.test(navigator.userAgent.toLowerCase())) )
							{
								tag_input.tag(
								  {
									placeholder:tag_input.attr('placeholder')
								  }
								);
							}
							else {
								//display a textarea for old IE, because it doesn't support this plugin or another one I tried!
								tag_input.after('<textarea id="'+tag_input.attr('id')+'" name="'+tag_input.attr('name')+'" rows="3">'+tag_input.val()+'</textarea>').remove();
								//$('#form-field-tags').autosize({append: "\n"});
							}
			{/literal}


							var max_filesize = {$max_filesize}; //maximum permitted file size
			{literal}


							$('#analysisType').on('click change', function(e){

								$("#remainingParams").css("display", "inline");

								var target = $(e.target).find('input[type=radio]');
								var which = target.val();

							//alert(which);

								if(which == 'genechip') {

									$("#GeneBedFile").show();
									$("#selfReporting").hide();

									$("#increase_sensitivity_div").hide();

									if ( $("#chipChIP").hasClass("active") ) {
										$("#chipChIP").removeClass("active");
									}
									if ( $("#chipChIP").hasClass("btn-success") ) {
										$("#chipChIP").removeClass("btn-success");
									}

									if ( !$("#geneChIP").hasClass("active") ) {
										$("#geneChIP").addClass("active");
									}
									if ( !$("#geneChIP").hasClass("btn-success") ) {
										$("#geneChIP").addClass("btn-success");
									}


									//$('#form-field-select-4').addClass('tag-input-style');

								} else if(which == 'chipchip'){

									$("#GeneBedFile").hide();
									$("#selfReporting").show();

									$("#increase_sensitivity_div").show();

									if ( $("#geneChIP").hasClass("active") ) {
										$("#geneChIP").removeClass("active");
									}
									if ( $("#geneChIP").hasClass("btn-success") ) {
										$("#geneChIP").removeClass("btn-success");
									}

									if ( !$("#chipChIP").hasClass("active") ) {
										$("#chipChIP").addClass("active");
									}
									if ( !$("#chipChIP").hasClass("btn-success") ) {
										$("#chipChIP").addClass("btn-success");
									}

								}
							});


							/* Are we submitting a BED file? */
/*
								$("#userBedSubmit").on('change', function() {
								if ( $("#userBedSubmit").is(':checked') ) {
									$("#bedConfig").css("display","inline");

									// inactivate submit button
							    	$("#formSubmitBtn").prop("disabled", true);
							    	$("#formSubmitBtn").removeClass("btn-success");

								} else {
									$("#bedConfig").css("display","none");

									// activate submit button
							    	$("#formSubmitBtn").prop("disabled", false);
							    	$("#formSubmitBtn").addClass("btn-success");
								}

							});
*/
							/* email address syntax validator */
							$("#emailAddress").keyup( function() {
								var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
								//alert( this.value );
								if ( regex.test(this.value) ) {

									$("#emailAddressDiv").removeClass("has-error");
									$("#emailAddressDiv").addClass("has-success");
									$("#userBedType").css("display","block");

								} else {
									if ( !$("#emailAddressDiv").hasClass("has-error") ) {
										$("#emailAddressDiv").addClass("has-error");
									}
									$("#userBedType").css("display","none");

									/* if submit is currently enabled, switch it off until valid email given */

								}
							});

							/* Which BED file type are we submitting? */
							$("#userBedType input[name=userBedType]").on("change", function(){

								if ( $("#userBedType input:radio[name=userBedType]:checked").val() == "chipchip" ) {
									/* submitting ChIP Peaks */
									$("#ChipBedFiles").css("display","inline");
									$("#GeneBedFiles").css("display","none");
									$("#userBedType").removeClass("has-error");
								}
								if ( $("#userBedType input:radio[name=userBedType]:checked").val() == "genechip" ) {
									/* submitting individual gene regions */
									$("#ChipBedFiles").css("display","none");
									$("#GeneBedFiles").css("display","inline");
									$("#userBedType").removeClass("has-error");
								}

							});


							/* BED file selection */
							$('.input_bedFile').ace_file_input({
								no_file:'Select a BED File ...',
								no_icon:'icon-warning-sign red',
								btn_choose:'Choose',
								btn_change:'Change',
								droppable:true,
								thumbnail:false, //| true | large
								before_change: function(files) {
									var filesize = files[0].size;
									if ( filesize > max_filesize ) {
										alert("Maximum allowed file size is " + max_filesize + " bytes. You tried to upload a file of " + filesize + " bytes.");
										return false;
									}
									return files;
								},
								before_remove : function(event) {
									/* removing a file that was added, so remove the Expt ID field */
									var $file_identifier = this.id + "_ID";
									var $file_divID = this.id + "_Div";

									/* The Expt ID Div class change */
									if ( $("#" + $file_identifier).hasClass("has-success") ) {
										$("#" + $file_identifier + "_Txt").val(null);
										$("#" + $file_identifier).removeClass("has-success");
									} else {
										/* remove the has-error class as we no longer have the*/
										$("#" + $file_identifier).removeClass("has-error");
									}
									if ( $("#" + $file_identifier).is(':visible') ) {
										$("#" + $file_identifier).css("display","none");
									}
									/* The file input Div class change */
									$("#" + $file_divID).removeClass("has-success");

									var $hasSuccess = 0;
									var $hasError = 0;
									/* If an Expt IDs input has a "has-success" class and no more exist having "has-error" class, we don't inactivate the button */
									$(".bedFile_IDs").each( function(i, obj) {
										if ( $(this).hasClass("has-error") ) {
											$hasError++;
										}
										if ( $(this).hasClass("has-success") ) {
											$hasSuccess++;
										}
									});
									return true;
								}
							}).on('change', function(event){

/*
								var $file_divID = event.target.id;
								var $file_identifier = $file_divID + "_ID";
*/
								var $file_ID = event.target.id;
								var $file_divID = $file_ID + "_Div";
								var $file_identifier = $file_ID + "_ID";

								if ( $(this).data('ace_input_files')[0] && $(this).data('ace_input_files')[0].size > 0 ) {

									/* a file has been selected */

									if ( !$("#" + $file_identifier).hasClass("has-success") ) {
										$("#" + $file_identifier).css("display","inline");
										$("#" + $file_identifier).addClass("has-error");


										/* once an Expt ID has been entered, we can remove the error and activate button */
										$("#" + $file_identifier + " input").keyup( function(){
											$("#" + $file_identifier).removeClass("has-error");
											$("#" + $file_identifier).addClass("has-success");

											var $hasErrors = false;
											/* make sure all Expt IDs inputs have no "has-error" class */
											$(".bedFile_IDs").each( function(i, obj) {
												if ( $(this).hasClass("has-error") ) {
													$hasErrors = 1;
												}
											});
										});
									}

									if ( $("#" + $file_divID).hasClass("has-error") ) {
										$("#" + $file_divID).removeClass("has-error");
									}

								} else {
									if ( $("#" + $file_identifier).is(':visible') ) {
										$("#" + $file_identifier).css("display","none");
									}
								}

							});

							/* auto-adding of BED file inputs, based on number selected */
							var maxFiles = {/literal}{$max_chip_bedfiles}{literal};
							var currentNumber = {/literal}{$min_chip_bedfiles}{literal};
							var minFiles = {/literal}{$min_chip_bedfiles}{literal};
							$('#bedFileNumber').ace_spinner({value:currentNumber,min:minFiles,max:maxFiles,step:1, on_sides: true, icon_up:'icon-plus smaller-75', icon_down:'icon-minus smaller-75', btn_up_class:'btn-success' , btn_down_class:'btn-danger'}).on('change', function(){

								var numFiles = $('#bedFileNumber').val();

								if ( numFiles < currentNumber ) {
									/* we decreased the number of bedfiles to add, so remove superfluous input */
									$("#bedFile" + currentNumber + "_File_Div").css('display','none');
									currentNumber--;
								}
								if ( numFiles > maxFiles ) {
									$('#bedFileNumber').val(maxFiles);
								}

								for ( var i = 1; i <= numFiles; i++ ) {
									if ( !$("#bedFile" + i + "_File_Div").is(':visible') ) {
										$("#bedFile" + i + "_File_Div").css('display','block');
										currentNumber++;
									}
								}
							});


						});

						jQuery.fn.extend({

							activateParams: function () {

//								$('#parameterSelect').css({ opacity: 1 });
								$("#step3").addClass("active");
								if ( !$("#step2").hasClass("complete") ) {
									$("#step2").addClass("complete");
								};

								/* Options to active */
								$("#title").prop("disabled", false);
								$("#userBedSubmit").prop("disabled", false);
								$("#omitHOT").prop("disabled", false);
								$("#selfReport").prop("disabled", false);

								/* activate help boxes */
								$(".param-help").css("display","block");



							},
							disableParams: function () {
								/* Reset all parameters and disable the Params box */
					    		$('#parameterSelect').css({ opacity: 0.5 });

								// disable Radios/text
					    		$("#title").prop("disabled", true);
					    		$("#userBedSubmit").prop("disabled", true);
					    		$("#userBedSubmit").prop("checked", false);
					    		$("#omitHOT").prop("disabled", true);
								$("#selfReport").prop("disabled", true);

								/* inactivate help boxes */
								$(".param-help").css("display","none");


								$("#bedConfig").css("display", "none");
								var bedFileNumber = $("#bedFileNumber").val();
								$("#bedConfig").find('input:text').val('');
								$("#bedFileNumber").val(bedFileNumber); //put the bedFileNumber back to what it was after input:text reset

								$(".has-status").removeClass('has-success');
								$(".has-status").addClass('has-error');
								$(".bedFile_IDs").removeClass("has-error"); //remove any "has-error" classes from invisible Expt IDs

								$("input[name=userBedType]").prop('checked', false);
								$("#userBedType").css('display','none');

								$("#ChipBedFiles").css('display','none');
								$("#GeneBedFiles").css('display','none');

								/* progress bar */
					    		$("#step3").removeClass("active");

					    		return true;
							}

						});
			{/literal}

					</script>

