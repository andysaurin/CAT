
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
							<h1 class="text">mouseCAT
								<small>
									<i class="icon-double-angle-right"></i>
									The <b>mouse</b> ENCODE <b>C</b>hIP <b>A</b>ssociation <b>T</b>ester
								</small>
							</h1>

							<span class="row">
								<p>
									mouseCAT allows you to easily compare your <i>Mus musculus</i> (mm9) ChIP peaks from your peak-called BED file with those from most all mouse <a href="https://genome.ucsc.edu/ENCODE/dataMatrix/encodeChipMatrixMouse.html" title="ENCODE data grid" target="_blank">ENCODE ChIP peaks</a> from cells and tissues.
								</p>
								<p>
									Peaks that share significantly common genomic binding sites score highly, allowing easy identification of proteins that show similar or identical genomic distributions from those that are distinct.
								</p>
							</span>
							<span class="row">
								<h5><a href="/CAT/" title="The ChIP Association Tester"><span class="text green"><strong>More information on the ChIP Association Tester can be found here.</strong></span></a></h5>
							</span>


							<div class="row">
								<h4 class="text blue lead">To use mouseCAT</h4>
								<ol>
									<li><strong>Select below the tissue(s) of interest.</strong></li>
									<li><strong>Datasets of ChIP'd proteins with pre-computed data available for these tissues can then be selected for analysis.</strong></li>
									<li><strong>Finally, you can simply obtain the association data selected, or upload your own <span class="red"><strong>mm9</strong></span>-mapped BED file(s).</strong></li>
								</ol>
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
						<div class="row-fluid">
							<div class="span12">
								<div class="widget-box">
									<div class="widget-header widget-header-blue widget-header-flat">
										<h2 class="blue">Let's get started!</h2>


									</div>

									<!-- Main Data Entry Widget Body -->
									<div class="widget-body">

										<!-- Progress Bar -->
										<div class="widget-main">
											<div id="fuelux-wizard" class="row-fluid" data-target="#step-container">
												<ul class="wizard-steps">
													<li data-target="#step1"  id="step1" class="active">
														<span class="step">1</span>
														<span class="title">Source Tissue</span>
													</li>

													<li data-target="#step2"  id="step2">
														<span class="step">2</span>
														<span class="title">Select Factors</span>
													</li>

													<li data-target="#step3" id="step3">
														<span class="step">3</span>
														<span class="title">Configure</span>
													</li>

												</ul>
											</div>
										</div>
										<!-- // Progress Bar -->


										<!-- Select Items row -->
										<div class="row">

											<form name="data_form" id="data_form" method="post" enctype="multipart/form-data">

												<div class="col-xs-12 col-sm-4">
													<!-- widget -->
													<div class="widget-box">
														<div class="widget-header">
															<h4>Select tissue source</h4>
															<div class="widget-toolbar">
																<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="The number of ENCODE ChIP datasets available with pre-computed association data for each tissue is shown to the right of the tissue." title="Select a tissue (or tissues) of ENCODE datasets to use">?</span>
																<a href="#" data-action="collapse">
																	<i class="icon-chevron-up"></i>
																</a>
															</div>
														</div>
														<!-- widget body -->
														<div class="widget-body">
															<div class="widget-main">

																<div id="tissues_accordion" class="accordion-style1 panel-group">
																<!-- Start tissue list -->
																{foreach from=$tissue_abbreviations key='abbreviation_key' item='abbreviation_array'}

																	<div class="panel panel-default">
																		<div class="panel-heading">
																			<h4 class="panel-title">
																				<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#tissues_accordion" href="#collapse_{$abbreviation_key|replace:' ':'_'}">
																					<i class="icon-angle-right bigger-110" data-icon-hide="icon-angle-down" data-icon-show="icon-angle-right"></i>
																					Select {$abbreviation_key}{if $abbreviation_key == 'Embryo'} stages{/if}
																					<span class="pull-right lighter badge badge-info">{$abbreviation_array.num_datasets}</span>
																					<!-- <i class="pull-right lighter">({$abbreviation_array.num_datasets})</i> -->
																				</a>
																			</h4>
																		</div>
																		<div class="panel-collapse collapse" id="collapse_{$abbreviation_key|replace:' ':'_'}">
																			<div class="panel-body row-fluid" style="padding-left:0;margin-left:0">
																				<input type="button" class="btn btn-link" id="{$abbreviation_key|replace:' ':'_'}" value="check all" />
																			{foreach from=$abbreviation_array.tissues key='tissue_id' item='tissue_array'}

																				<div class="checkbox clearfix">
																					<label class="pull-left col-sm-10" style="padding-right:0;margin-right:0">
																						{if $tissue_array->tissue_name|in_array:$tissue_offline || $tissue_id|in_array:$tissue_offline}
																						<i class="icon-warning-sign red bigger-130" data-rel="popover" data-trigger="hover" data-placement="top" data-content="New datasets are currently being imported for this tissue making it unavailable for use. Please check back later."></i>
																						<span class="lbl">
																							<span data-rel="popover" data-trigger="hover" data-placement="top" data-content="New datasets are currently being imported for this tissue making it unavailable for use. Please check back later.">
																								&nbsp;&nbsp;{$tissue_array->tissue_name}&nbsp;
																							</span>
																						</span>
																						{else}
																						<input name="tissues[{$tissue_id}]" type="checkbox" class="ace tissueSelect" />
																						<span class="lbl">
																							<span data-rel="popover" data-trigger="hover" data-placement="top" data-content="{$tissue_array->modencode_classification}">
																								&nbsp;&nbsp;{$tissue_array->tissue_name}&nbsp;
																							</span>
																						</span>
																						{/if}
{* 																						<span class="pull-right lighter badge badge-success">{$tissue_array->num_datasets}</span> *}
{* 																						<div class="lighter badge badge-success pull-right">{$tissue_array->num_datasets}</div> *}
																					</label>
																					<label class="pull-right col-sm-2" style="padding-right:0;margin-right:0">
																						<i class="lighter badge badge-success pull-right">{$tissue_array->num_datasets}</i>
																					</label>
																				</div>
																			{/foreach}

																			</div>
																		</div>
																	</div>

																{/foreach}
																<!-- //Start tissue list -->
																</div>

															</div>
														</div>
														<!-- //widget body -->

													</div>
													<!-- //widget -->
												</div>


												<div class="col-xs-12 col-sm-4" id="factorSelect" style="opacity:0.5">
													<!-- widget -->
													<div class="widget-box">
														<div class="widget-header">
															<h4>Select ChIP'd proteins</h4>
															<div class="widget-toolbar">
																<span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="The immunoprecipitated protein is selectable if there are pre-computed association data available for the selected tissue(s). The number of ENCODE ChIP datasets available with pre-computed association data will appear to the right the protein." title="Select the proteins you wish to study.">?</span>
																<a href="#" data-action="collapse">
																	<i class="icon-chevron-up"></i>
																</a>
															</div>
														</div>

														<!-- Widget body -->
														<div class="widget-body">
															<div class="widget-main">

																<div id="factors_accordion" class="accordion-style1 panel-group">

																<!-- Start protein list -->
																{foreach from=$factor_groups key="factor_group" item='factor_group_array'}

																	<div class="panel panel-default">
																		<div class="panel-heading">
																			<h4 class="panel-title">
																				<a class="accordion-toggle collapsed factorGroup_accordion" data-toggle="" id="factorGroup_{$factor_group}_accordion" data-parent="#factors_accordion">
																					<i class="icon-angle-right bigger-110" data-icon-hide="icon-angle-down" data-icon-show="icon-angle-right"></i>
																					Select {$factor_group_array.name}
																					<span class="pull-right lighter badge badge-info factorGroup_numResults" style="display:none" id="factorGroup_{$factor_group}_numResults">0</span>
																					<!-- <i class="pull-right lighter">(<i id="factorGroup_{$factor_group}_numResults" class="factorGroup_numResults">0</i>)</i> -->
																				</a>
																			</h4>
																		</div>
																		<div class="panel-collapse collapse" id="collapse_{$factor_group}">
																			<div class="panel-body">
																				<input type="button" class="btn btn-link" id="{$factor_group}" value="check all available" />
																			{foreach from=$factor_group_array.factors item='factor_array'}

																				<div class="checkbox clearfix factor_selectDiv" id="factor_{$factor_array.id}_selectDiv" style="opacity:0.2">
																					<label class="pull-left col-sm-10" style="padding-right:0;margin-right:0">
																						<input id="factor_{$factor_array.id}_select" name="factors[{$factor_array.id}]" type="checkbox" class="ace factor_select" disabled />
																						<span class="lbl">&nbsp;&nbsp;{$factor_array.name}&nbsp;</span>
																						{if $factor_array.fullname}<small><small><i class="lighter">{$factor_array.fullname}&nbsp;</i></small></small>{/if}
{*																						<span class="pull-right lighter badge badge-success factor_numResults" style="display:none" id="factor_{$factor_array.id}_numResults">0</span>
																						<!-- <i class="pull-right lighter">(<i class="factor_numResults" id="factor_{$factor_array.id}_numResults">0</i>)</i> -->
*}
																					</label>
																					<label class="pull-right col-sm-2" style="padding-right:0;margin-right:0">
																						<i class="pull-right lighter badge badge-success factor_numResults" style="display:none" id="factor_{$factor_array.id}_numResults">0</i>
																					</label>
																				</div>
																			{/foreach}

																			</div>
																		</div>
																	</div>
																{/foreach}

																<!-- //Start protein list -->
																</div>

															</div>
														</div>
														<!-- //widget body -->
													</div>
													<!-- //widget -->
												</div>

												<!-- Configure Parameters -->
												<div class="col-xs-12 col-sm-4" id="parameterSelect" style="opacity:0.5">
													<!-- widget -->
													<div class="widget-box">
														<div class="widget-header">
															<h4>Configure Parameters</h4>
															<div class="widget-toolbar">
																<a href="#" data-action="collapse">
																	<i class="icon-chevron-up"></i>
																</a>
															</div>
														</div>

														<div class="widget-body">
															<div class="widget-main" >

																<div class="form-group row">
																	<label class="col-xs-3 control-label no-padding-right" for="title"> Title</label>
																	<div class="col-xs-9">
																		 <input name="title" type="text" id="title" class="col-xs-10" placeholder="Give a title to the run" disabled />
																		<span class="lbl"></span>
																	</div>

																</div>

																<div class="form-group row" id="increase_sensitivity_div">
																	<label class="col-xs-3" for="increase_sensitivity"> <a href="/CAT/#ChIP_Association_Score">Score for:</a> </label>
																	<div class="col-xs-8">
																		<select class="col-xs-12" name="increase_sensitivity" class="form-control no-padding-right" id="increase_sensitivity" disabled>
																			<option value="on">Highly similar peaks</option>
																			<option value="off">Overlapping Peaks</option>
																		</select>
																	</div>
																	<div class="col-xs-1 no-padding-left">
																		<span  class="help-button param-help" style="display:none" data-rel="popover" data-trigger="hover" data-placement="left" data-html="true" title="" data-content="<p>Scoring for <b>overlapping peaks</b><br>&nbsp;&nbsp;&nbsp;&nbsp;will highlight pairwise peaks showing significant overlap (e.g. factors associating with histone marks).</p>Scoring for <b>highly similar peaks</b><br>&nbsp;&nbsp;&nbsp;&nbsp;will show significant overlap where each peak's start/stop coordinates are highly similar (e.g. transcription co-factors).</p>">?</span>
																	</div>
																</div>


																<div class="form-group row">
																	<span class="col-xs-5 control-label no-padding-right" for="userBedSubmit"> Submit your own BED file(s)?</span>
																	<div class="col-xs-2 no-padding-left">
																		 <input name="userBedSubmit" class="ace ace-switch ace-switch-2" type="checkbox" id="userBedSubmit"  disabled />
																		<span class="lbl"></span>
																	</div>
																	<div class="col-xs-4">&nbsp;</div>
																	<div class="col-xs-1 no-padding-left">
																		<span  class="help-button param-help" style="display:none" data-rel="popover" data-trigger="hover" data-placement="left" title="Compare your data with ENCODE data?" data-content="If you simply want to view the pre-computed data, leave this option unchecked.<br>However, if you wish to include your own BED-formatted ChIP peaks or genomic regions in the analysis, check this option.">?</span>
																	</div>
																</div>

																<div class="form-group row" id="bedConfig" style="display:none;">

																	<div class="input-group form-group has-status has-error" id="emailAddressDiv">
																		<span class="input-group-addon">
																			<i class="icon-envelope"></i>
																		</span>

																		<input class="form-control input-mask-envelope" type="text" name="emailAddress" autocomplete="off" id="emailAddress" placeholder="Email address to send results to" />
																	</div>

																	<div class="form-group row has-status has-error" id="userBedType" style="display:none">
																		<div class="col-xs-4">
																			<label class="control-label no-padding-right"> BED File Type?</label>
																		</div>
																		<div class="col-xs-8">
																			<div>
																				<input name="analysisType" class="ace ace-switch ace-switch-2" type="radio" value="chipchip" />
																				<span class="lbl">&nbsp;&nbsp;ChIP peaks</span>
																			</div>
																			<div>
																				 <input name="analysisType" class="ace ace-switch ace-switch-2" type="radio" value="genechip"  />
																				<span class="lbl">&nbsp;&nbsp;Individual Genomic Regions</span>
																			</div>
																		</div>
																	</div>

																	<div id="ChipBedFiles" style="display:none">
																		<div class="form-group row" id="numberBedFiles">

																			<label class="col-xs-9 control-label no-padding-right" for="bedFileNumber"> Number of ChIP BED files to submit? </label>
																			<div class="col-xs-3">
																				<input type="text" class="input-mini" id="bedFileNumber" style="min-width:25px;" readonly="" />
																			</div>
																		</div>

																		<!-- <div class="form-group row"> -->

																		<div class="row">
																			<div class="col-xs-3 bedFile_IDs has-status" id="bedFile1_File_ID" style="display:none">
																				<span class="block input-icon input-icon-left">
																					<input name="ChipBedFiles[file_id][1]" type="text" class="input-small" id="bedFile1_File_ID_Txt" placeholder="Expt. ID" data-rel="popover" data-trigger="hover" data-placement="top" title="Enter an experimental identifier for this dataset." data-content="This ID will be used as the column/row header in the resulting data matrix." />
																					<i class="icon-info-sign"></i>
																				</span>
																			</div>
																			<div class="col-xs-9" id="bedFile1_File_Div" style="display:inline">
																				<input name="ChipBedFiles[file][1]" type="file" class="input_bedFile" id="bedFile1_File" />
																			</div>
																		</div>

																		{for $i=2 to $max_bed_files}

																		<div class="row">
																			<div class="col-xs-3 bedFile_IDs has-status" id="bedFile{$i}_File_ID" style="display:none">
																				<span class="block input-icon input-icon-left">
																					<input name="ChipBedFiles[file_id][{$i}]" type="text" class="input-small" id="bedFile{$i}_File_ID_Txt" placeholder="Expt. ID" data-rel="popover" data-trigger="hover" data-placement="top" title="Enter an experimental identifier for this dataset." data-content="This ID will be used as the column/row header in the resulting data matrix." />
																					<i class="icon-info-sign"></i>
																				</span>
																			</div>
																			<div class="col-xs-9" id="bedFile{$i}_File_Div" style="display:none">
																				<input name="ChipBedFiles[file][{$i}]" type="file" class="input_bedFile" id="bedFile{$i}_File" />
																			</div>
																		</div>

																		{/for}

																		<!-- </div> -->
																	</div><!-- /#ChipBedFiles -->

																	<div id="GeneBedFiles" style="display:none">
																		<div class="form-group row">

{*
																			<div class="col-xs-4 input-group  form-group bedFile_IDs has-status" id="geneBedFile_File_ID"  style="display:none">
																				<span class="block input-icon input-icon-left">
 																					<input name="GeneBedFiles[file_id][1]" type="text" class="input-small" id="geneBedFile_File_ID_Txt" placeholder="Expt. ID" data-rel="popover" data-trigger="hover" data-placement="top" title="Enter an experimental identifier for this dataset." data-content="This ID will be used as the column/row header in the resulting data matrix." />
																					<i class="icon-info-sign"></i>
																				</span>
																			</div>
*}

																			<div class="col-xs-8 input-group  form-group" id="geneBedFile_File_Div" style="display:inline">
																				<input name="GeneBedFile" type="file" class="input_bedFile" id="geneBedFile_File" />
																			</div>

																		</div>
																	</div><!-- /#GeneBedFiles -->

																</div><!-- /#bedConfig -->
{*

																<div class="form-group row">
																	<label class="col-xs-8 control-label no-padding-right" for="omitHOT"> Omit HOT regions?</label>
																	<div class="col-xs-3 ">
																		<input name="omitHOT" class="ace ace-switch ace-switch-2" type="checkbox" id="omitHOT" disabled />
																		<span class="lbl"></span>
																	</div>
																	<div class="col-xs-1 no-padding-left">
																		<span  class="help-button param-help" style="display:none" data-rel="popover" data-trigger="hover" data-placement="left" title="Omit genomic regions considered as transcription factor (TF) High Occupancy Targets. Select this option if studying transcription factors for increased sensitivity." data-content="Extensive overlap in the binding profiles of multiple TFs has revealed highly occupied target (HOT) regions or hotspots, which have been proposed as stable genomic regions that facilitate binding of additional TFs at their motifs or nonspecifically. In studying TF colocalisation, you may wish to eliminate HOT regions that are known to bind more than 8 TFs by checking this option.">?</span>
																	</div>
																</div>
*}
																<div class="form-group row">
																	<span class="col-xs-7">Omit certain datasets?</span>
																	<div class="col-xs-11">
																		<input type="text" name="omit_factors" id="form-field-tags" placeholder="ENCODE IDs ..." />

																	</div>
																	<div class="col-xs-1 no-padding-left">
																		<span  class="help-button param-help" style="display:none" data-rel="popover" data-trigger="hover" data-placement="left" title="Omit certain datasets." data-html="true" data-content="If you've previously run mouseCAT and found that multiple ENCODE replicates are biasing the clustering analysis, you can omit replicates by entering their ENCODE IDs here.<br><br>The ENCODE IDs are given in parentheses next to the protein name in the output heatmap.<br>Enter an ID number followed by return for each dataset to omit from the results.">?</span>
																	</div>
																</div>

																<div class="form-group row">
																	<label class="col-xs-5 control-label no-padding-right" for="selfReport">Score "Self" <i>vs</i> "Self" as</label>
																	<div class="col-xs-6  no-padding-left">
																		<select class="width-40" name="selfReport" class="form-control no-padding-right" id="selfReport" disabled>
																			<option value="NA" selected>NA</option>
																			<option value="0">0</option>
																			<option value="score">Actual Score</option>
																		</select>
																	</div>
																	<div class="col-xs-1 no-padding-left">
																		<span class="help-button param-help" style="display:none" data-rel="popover" data-trigger="hover" data-placement="left" title="How to report Self vs Self score in data matrix?" data-content='In analyses of an "all vs all" data matrix, how do you wish to represent the value of "self vs self"? Using the actual score may skew the analysis since the value for "self vs self" in terms of genomic association will always be the highest score for that factor. Note: this can be ignored if you are providing an "Individual Genomic Regions" BED file, since the resulting data is not an "all vs all" data matrix.'>?</span>
																	</div>
																</div>

																<div class="form-group row" id="formSubmit">
																	<div class="col-md-offset-3 col-md-9">
																		<button type="submit" class="btn btn-sm" name="formSubmitBtn" id="formSubmitBtn" disabled>
																			Calculate Data
																			<i class="icon-arrow-right icon-on-right bigger-110"></i>
																		</button>
																	</div>

																</div>

															</div><!-- /.widget-main -->

														</div> <!-- /.widget-body -->

													</div>
													<!-- //widget -->

												</div>
												<!-- Configure Parameters -->

											</form>

										</div>
										<!-- //Select Items row -->

									</div>
									<!-- //Main Data Entry Widget Body -->

								</div><!-- //widget-box -->
							</div><!-- //span-12 -->

						</div>
						<!-- End Main Data Entry -->

					</div>
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

			{foreach from=$tissue_abbreviations key='abbreviation' item='array'}
			{literal}
						    $('#{/literal}{$abbreviation|replace:' ':'_'}{literal}:button').click(function(){
						    	var checked = !$(this).data('checked');
								$('#collapse_{/literal}{$abbreviation|replace:' ':'_'}{literal} input:checkbox:not(:disabled)').prop('checked', checked).change();
								$(this).val(checked ? 'uncheck all' : 'check all' )
								$(this).data('checked', checked);

						    });
			{/literal}

			{/foreach}
			{foreach from=$factor_groups key='factor_group' item='array'}
			{literal}
						    $('#{/literal}{$factor_group}{literal}:button').click(function(){
						    	var checked = !$(this).data('checked');
								$('#collapse_{/literal}{$factor_group}{literal} input:checkbox:not(:disabled)').prop('checked', checked).change();
								$(this).val(checked ? 'uncheck all' : 'check all available' )
								$(this).data('checked', checked);

						    });
			{/literal}

			{/foreach}
							var max_filesize = {$max_filesize}; //maximum permitted file size
			{literal}
						    /* Activate/Disable Protein Factors based on data available from tissue selected */
						    var numTissueChecked = 0;
						    var prevTissueChecked = 0;
						    $('.tissueSelect:checkbox').on('change', function () {

						    	/* the next 2 vars prevent "check all" firing the ajax query multiple times at once */
						    	prevTissueChecked = numTissueChecked;
						    	numTissueChecked = $('.tissueSelect:checkbox:checked').length;

						    	if ( numTissueChecked != prevTissueChecked ) {
							    	if ( numTissueChecked == 0 ) {

							    		$(this).disableParams();

							    		/* progress bar */
							    		$("#step3").removeClass("active");
							    		$("#step2").removeClass("active");
							    		$("#step2").removeClass("complete");
							    		$("#step1").removeClass("complete");

							    		/* all checkboxes have been unchecked, so update 0 results on ChIP factors and hide parameters*/
							    		$('#factorSelect').css({ opacity: 0.5 });

							    		$('.factorGroup_accordion').removeAttr("data-toggle");
							    		$('.factorGroup_accordion').removeAttr("href");

							    		$('.factor_numResults').html("0");
							    		$('.factor_numResults').css("display", "none");

							    		$('.factorGroup_accordion').css({ opacity: 0.5 });

							    		$('.factorGroup_numResults').css("display", "none");
							    		$('.factorGroup_numResults').html("0");

							    		/* individual factor tallies */
										$('.factor_select').prop("disabled", true);
							    		$('.factor_selectDiv').css({ opacity: 0.2 });
							    		$('#factors_accordion input:checkbox').prop('checked', false);
										$('#factors_accordion :input').prop("disabled", true);
										$('.factor_numResults').css("display", "none");



							    	} else {

							    		/* we have checked 1 or more boxes */
										$('#factorSelect').css({ opacity: 1 });
										$("#step1").addClass("complete");
										$("#step2").addClass("active");

									    $.ajax({
											type: "POST",
											url: ajaxURL + "&do=get_factor_numbers",
											data: $("#data_form").serialize(),
											success: function(data)
											{
												var parsed = $.parseJSON(data);

												/* factor group tallies */
												$.each(parsed.factor_group_counts, function(index, element) {
													if (element.total_counts > 0) {
														$('#'+index).prop('disabled',false);
														$('#'+index).val("check all available");
														$('#'+index).data("checked", false);

														$('#factorGroup_'+index+'_numResults').css("display", "block");
														$('#factorGroup_'+index+'_numResults').html(element.total_counts);
														$('#factorGroup_'+index+'_accordion').css({ opacity: 1 });
														$('#factorGroup_'+index+'_accordion').attr("href", "#collapse_"+index);
														$('#factorGroup_'+index+'_accordion').attr("data-toggle", "collapse");

													} else {
														$('#factorGroup_'+index+'_numResults').css("display", "none");
														$('#factorGroup_'+index+'_numResults').html("0");
														$('#factorGroup_'+index+'_accordion').css({ opacity: 0.5 });
														$('#factorGroup_'+index+'_accordion').removeAttr("href");
														$('#factorGroup_'+index+'_accordion').removeAttr("data-toggle");
														$('#collapse_'+index+' input:checkbox').prop('checked', false); //uncheck it in case previously checked
													}
												});
												/* individual factor tallies */
												$('.factor_selectDiv').css({ opacity: 0.2 });
												$('.factor_selectDiv :input').prop("disabled", true);
												$('.factor_numResults').css("display", "none");

												$.each(parsed.factor_counts, function(index, element) {
													$('#factor_'+index+'_numResults').html(element);
													$('#factor_'+index+'_numResults').css("display", "block");
													$('#factor_'+index+'_select').removeAttr("disabled")
													$('#factor_'+index+'_selectDiv').css({ opacity: 1 });
												});

											}
										});

							    	}
							    }

							});

							/* Activate/Disable Parameters based on whether any factors have been selected */
							var numFactorsChecked = 0;
							var prevFactorsChecked = 0;
							$('.factor_select:checkbox').on('change', function () {

						    	/* the next 2 vars prevent "check all" firing the ajax query multiple times at once */
						    	prevFactorsChecked = numFactorsChecked;
						    	numFactorsChecked = $('.factor_select:checkbox:checked').length;

						    	if ( numFactorsChecked != prevFactorsChecked ) {
							    	if ( numFactorsChecked == 0 ) {

							    		$(this).disableParams();

										/* progress bar */
							    		$("#step2").removeClass("complete");


							    	} else {

							    		//update progress bar
							    		$("#step2").addClass("complete");

							    		/* we have checked 1 or more factor boxes */
							    		$(this).activateParams();

							    	}
							    }

							});

							/* Are we submitting a BED file? */
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
									if ( !$("#formSubmitBtn").is(":disabled") ) {
										// inactivate submit button
										$("#formSubmitBtn").prop("disabled", true);
										$("#formSubmitBtn").removeClass("btn-success");
									}
								}
							});

							/* Which BED file type are we submitting? */
							$("#userBedType input[name=analysisType]").on("change", function(){

								if ( $("#userBedType input:radio[name=analysisType]:checked").val() == "chipchip" ) {
									/* submitting ChIP Peaks */
									$("#increase_sensitivity_div").css("display","inline");
									$("#ChipBedFiles").css("display","inline");
									$("#GeneBedFiles").css("display","none");
									$("#userBedType").removeClass("has-error");
								}
								if ( $("#userBedType input:radio[name=analysisType]:checked").val() == "genechip" ) {
									/* submitting individual gene regions */
									$("#increase_sensitivity_div").css("display","none");
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
										if ( $hasSuccess > 0 &&  $hasError == 0 ) { //at least one bed file is selected with an Expt ID
											if ( $("#formSubmitBtn").is(":disabled") ) {
												// inactivate submit button
												$("#formSubmitBtn").prop("disabled", false);
												$("#formSubmitBtn").addClass("btn-success");
											}
										} else {
											/* if submit is currently enabled, switch it off */
											if ( !$("#formSubmitBtn").is(":disabled") ) {
												// inactivate submit button
												$("#formSubmitBtn").prop("disabled", false);
												$("#formSubmitBtn").removeClass("btn-success");
											}
										}
										return true;


								}
							}).on('change', function(event){

/*
								var $file_divID = event.target.id;
								var $file_identifier = $file_divID + "_ID";
*/
								if ( $("#userBedType input:radio[name=analysisType]:checked").val() == "genechip" ) {
									/* we don't have Expt IDs in gene-cgip analyses

									/*activate the submit button*/
									$("#formSubmitBtn").prop("disabled", false);
									$("#formSubmitBtn").addClass("btn-success");

								} else {

									var $file_ID = event.target.id;
									var $file_divID = $file_ID + "_Div";
									var $file_identifier = $file_ID + "_ID";

									if ( $("#userBedType input:radio[name=analysisType]:checked").val() == "chipchip" && $(this).data('ace_input_files')[0] && $(this).data('ace_input_files')[0].size > 0 ) {

										/* a file has been selected */

										if ( !$("#" + $file_identifier).hasClass("has-success") ) {
											$("#" + $file_identifier).css("display","inline");
											$("#" + $file_identifier).addClass("has-error");

											/* if submit is currently enabled, switch it off */
											if ( !$("#formSubmitBtn").is(":disabled") ) {
												// inactivate submit button
												$("#formSubmitBtn").prop("disabled", true);
												$("#formSubmitBtn").removeClass("btn-success");
											}

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
												if ( $hasErrors != 1 ) {
													/*activate the submit button*/
													$("#formSubmitBtn").prop("disabled", false);
													$("#formSubmitBtn").addClass("btn-success");
												}
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
								}
							});

							/* auto-adding of BED file inputs, based on number selected */
							var maxFiles = {/literal}{$max_bed_files}{literal};
							var currentNumber = 1;
							$('#bedFileNumber').ace_spinner({value:1,min:1,max:maxFiles,step:1, btn_up_class:'btn-info' , btn_down_class:'btn-info'}).on('change', function(){

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

								$('#parameterSelect').css({ opacity: 1 });
								$("#step3").addClass("active");
								if ( !$("#step2").hasClass("complete") ) {
									$("#step2").addClass("complete");
								};

								/* Options to active */
								$("#title").prop("disabled", false);
								$("#userBedSubmit").prop("disabled", false);
								$("#increase_sensitivity").prop("disabled", false);
								$("#omitHOT").prop("disabled", false);
								$("#selfReport").prop("disabled", false);

								/* activate help boxes */
								$(".param-help").css("display","block");

						    	/* Submit button is initially active */
						    	$("#formSubmitBtn").removeAttr("disabled");
						    	$("#formSubmitBtn").addClass("btn-success");


							},
							disableParams: function () {
								/* Reset all parameters and disable the Params box */
					    		$('#parameterSelect').css({ opacity: 0.5 });

								// disable Radios/text
					    		$("#title").prop("disabled", true);
					    		$("#userBedSubmit").prop("disabled", true);
					    		$("#userBedSubmit").prop("checked", false);
					    		$("#increase_sensitivity").prop("disabled", true);
					    		$("#omitHOT").prop("disabled", true);
								$("#selfReport").prop("disabled", true);

								/* inactivate help boxes */
								$(".param-help").css("display","none");

					    		// inactivate submit button
								$("#formSubmitBtn").prop("disabled", true);
								$("#formSubmitBtn").removeClass("btn-success");

								$("#bedConfig").css("display", "none");
								var bedFileNumber = $("#bedFileNumber").val();
								$("#bedConfig").find('input:text').val('');
								$("#bedFileNumber").val(bedFileNumber); //put the bedFileNumber back to what it was after input:text reset

								$(".has-status").removeClass('has-success');
								$(".has-status").addClass('has-error');
								$(".bedFile_IDs").removeClass("has-error"); //remove any "has-error" classes from invisible Expt IDs

								$("input[name=analysisType]").prop('checked', false);
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

