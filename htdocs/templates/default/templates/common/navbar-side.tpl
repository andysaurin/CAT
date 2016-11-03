				<div class="sidebar sidebar-fixed" id="sidebar">

{*
					<div class="sidebar-shortcuts" id="sidebar-shortcuts">
						<div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
							<button class="btn btn-success">
								<i class="icon-signal"></i>
							</button>

							<button class="btn btn-info">
								<i class="icon-pencil"></i>
							</button>

							<button class="btn btn-warning">
								<i class="icon-group"></i>
							</button>

							<button class="btn btn-danger">
								<i class="icon-cogs"></i>
							</button>
						</div>

						<div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
							<span class="btn btn-success"></span>

							<span class="btn btn-info"></span>

							<span class="btn btn-warning"></span>

							<span class="btn btn-danger"></span>
						</div>
					</div><!-- #sidebar-shortcuts -->
*}
					<div class="sidebar-shortcuts" id="sidebar-shortcuts">
						&nbsp;
					</div>

					<ul class="nav nav-list">
						<li{if $module=='home'} class="active"{/if}>
							<a href="/">
								<i class="icon-dashboard"></i>
								<span class="menu-text"> Dashboard </span>
							</a>
						</li>

						<li>
							<a href="http://galaxy.biotools.fr/" target="_blank">
								<i style="background:url('/images/galaxy-icon-small.png') no-repeat;margin-left:5px;"><img src="/images/1pixel.gif" style="border:0; width:27px; height:20px"></i>
								<span class="menu-text"> Galaxy </span>
							</a>
						</li>
{*
						<li{if $module=='comparison'} class="active"{/if}>
							<a href="#" class="dropdown-toggle">
								<i class="icon-list"></i>
								<span class="menu-text"> Data Comparison </span>

								<b class="arrow icon-angle-down"></b>

							</a>
							<ul class="submenu">
								<li{if $class=='venny'} class="active"{/if}>
									<a href="/comparison/venny">
										<i class="icon-double-angle-right"></i>
										Venn Diagrams
									</a>
								</li>
							</ul>
						</li>
*}
						<li{if $module=='CAT'} class="active"{/if}>

							<a href="#" class="dropdown-toggle">
								<i style="background:url('/images/heatmap_small.png') no-repeat;margin-left:5px;"><img src="/images/1pixel.gif" style="border:0; width:27px; height:20px"></i>
								<span class="menu-text"> ChIP Association Tester </span>
								<b class="arrow icon-angle-down"></b>
							</a>

							<ul class="submenu">
								<li{if $module=='CAT' && ($class=='index' || !$class) } class="active"{/if}>
									<a href="/CAT/">
										<i class="icon-double-angle-right"></i>
										<span class="menu-text"> About CAT </span>
									</a>
								</li>

								<li{if $class=='drosoCAT'} class="active"{/if}>
									<a href="/CAT/drosoCAT">
										<i class="icon-double-angle-right"></i>
										<span class="menu-text"> drosoCAT </span>
									</a>
								</li>

								<li{if $class=='mouseCAT'} class="active"{/if}>
									<a href="/CAT/mouseCAT">
										<i class="icon-double-angle-right"></i>
										<span class="menu-text"> mouseCAT </span>
									</a>
								</li>

								<li{if $class=='humanCAT'} class="active"{/if}>
									<a href="/CAT/humanCAT">
										<i class="icon-double-angle-right"></i>
										<span class="menu-text"> humanCAT </span>
									</a>
								</li>

								<li{if $class=='webCAT'} class="active"{/if}>
									<a href="/CAT/webCAT">
										<i class="icon-double-angle-right"></i>
										<span class="menu-text"> webCAT </span>
									</a>
								</li>
							</ul>
						</li>

{*
						<li{if $module=='webcat'} class="active"{/if}>
							<a href="/webcat/">
								<i style="background:url('/images/heatmap_small.png') no-repeat;margin-left:5px;"><img src="/images/1pixel.gif" style="border:0; width:27px; height:20px"></i>
								<span class="menu-text"> webCAT </span>
							</a>
						</li>
*}

						<li{if $module=='drosophila'} class="active"{/if}>

							<a href="#" class="dropdown-toggle">
								<i style="background:url('/images/drosophila-icon-small2.png') no-repeat;margin-left:5px;"><img src="/images/1pixel.gif" style="border:0; width:27px; height:20px"></i>
								<span class="menu-text">Drosophila</span>
								<b class="arrow icon-angle-down"></b>
							</a>

							<ul class="submenu">
								<li{if $class=='phenotypes'} class="active"{/if}>
									<a href="/drosophila/phenotypes">
										<i class="icon-double-angle-right"></i>
										Phenotype Search
									</a>
								</li>

								<li{if $class=='fbgn_converter'} class="active"{/if}>
									<a href="/drosophila/fbgn_converter">
										<i class="icon-double-angle-right"></i>
										Flybase ID converter
									</a>
								</li>

								<li{if $class=='genetic_interactions'} class="active"{/if}>
									<a href="/drosophila/genetic_interactions">
										<i class="icon-double-angle-right"></i>
										Genetic Interactions search
									</a>
								</li>

								<li{if $class=='protein_interactions'} class="active"{/if}>
									<a href="/drosophila/protein_interactions">
										<i class="icon-double-angle-right"></i>
										Protein Interactions search
									</a>
								</li>

								<li{if $class=='ppi_cytoscape'} class="active"{/if}>
									<a href="/drosophila/ppi_cytoscape">
										<i class="icon-double-angle-right"></i>
										Protein Interacting pairs
									</a>
								</li>

								<li{if $class=='fbgn_to_bed'} class="active"{/if}>
									<a href="/drosophila/fbgn_to_bed">
										<i class="icon-double-angle-right"></i>
										FBgn ID to BED format
									</a>
								</li>

								<li{if $class=='human_disease_model_network'} class="active"{/if}>
									<a href="/drosophila/human_disease_model_network">
										<i class="icon-double-angle-right"></i>
										Human Disease Models
									</a>
								</li>

							</ul>
						</li>


						<li{if $module=='mouse'} class="active"{/if}>

							<a href="#" class="dropdown-toggle">
								<i style="background:url('/images/mouse-icon-small2.png') no-repeat;margin-left:5px;"><img src="/images/1pixel.gif" style="border:0; width:27px; height:20px"></i>
								<span class="menu-text">Mouse</span>
								<b class="arrow icon-angle-down"></b>
							</a>

							<ul class="submenu">

								<li{if module=='mouse' && $class=='ucsc_id_converter'} class="active"{/if}>
									<a href="/mouse/ucsc_id_converter">
										<i class="icon-double-angle-right"></i>
										UCSC ID Converter
									</a>
								</li>

								<li{if module=='mouse' && $class=='refseq_symbol_converter'} class="active"{/if}>
									<a href="/mouse/refseq_symbol_converter">
										<i class="icon-double-angle-right"></i>
										refSeq to Symbol Converter
									</a>
								</li>

							</ul>
						</li>

						<li{if $module=='human'} class="active"{/if}>

							<a href="#" class="dropdown-toggle">
								<i style="background:url('/images/vitruvian_man_icon2.png') no-repeat;margin-left:5px;"><img src="/images/1pixel.gif" style="border:0; width:27px; height:20px"></i>
								<span class="menu-text">Human</span>
								<b class="arrow icon-angle-down"></b>
							</a>

							<ul class="submenu">

								<li{if $module=='human' && $class=='ucsc_id_converter'} class="active"{/if}>
									<a href="/human/ucsc_id_converter">
										<i class="icon-double-angle-right"></i>
										UCSC ID Converter
									</a>
								</li>

								<li{if module=='human' && $class=='refseq_symbol_converter'} class="active"{/if}>
									<a href="/human/refseq_symbol_converter">
										<i class="icon-double-angle-right"></i>
										refSeq to Symbol Converter
									</a>
								</li>

							</ul>
						</li>

						<li{if $module=='chicken'} class="active"{/if}>

							<a href="#" class="dropdown-toggle">
								<i style="background:url('/images/chick_icon.jpg') no-repeat;margin-left:5px;"><img src="/images/1pixel.gif" style="border:0; width:27px; height:20px"></i>
								<span class="menu-text">Chicken</span>
								<b class="arrow icon-angle-down"></b>
							</a>

							<ul class="submenu">

								<li{if $module=='chicken' && $class=='ensembl_symbol_converter'} class="active"{/if}>
									<a href="/chicken/ensembl_symbol_converter">
										<i class="icon-double-angle-right"></i>
										ENSEMBL Gene ID Converter
									</a>
								</li>

							</ul>
						</li>

						<li{if $module=='pubmed'} class="active"{/if}>

							<a href="#" class="dropdown-toggle">
								<i style="background:url('/images/pubmed.png') no-repeat;margin-left:5px;"><img src="/images/1pixel.gif" style="border:0; width:27px; height:20px"></i>
								<span class="menu-text">PubMed</span>
								<b class="arrow icon-angle-down"></b>
							</a>

							<ul class="submenu">

								<li{if $class=='search'} class="active"{/if}>
									<a href="/pubmed/search">
										<i class="icon-double-angle-right"></i>
										Basic Search
									</a>
								</li>
								<li{if $class=='multi_search'} class="active"{/if}>
									<a href="/pubmed/multi_search">
										<i class="icon-double-angle-right"></i>
										Multi-Keyword Search
									</a>
								</li>

							</ul>
						</li>

						<li{if $module=='misc'} class="active"{/if}>
							<a href="#" class="dropdown-toggle">
								<i class="icon-tag"></i>
								<span class="menu-text"> Misc. Tools </span>

								<b class="arrow icon-angle-down"></b>
							</a>

							<ul class="submenu">
								<li{if $class=='venny'} class="active"{/if}>
									<a href="/misc/venny">
										<i class="icon-double-angle-right"></i>
										Venn Diagrams
									</a>
								</li>
								<li{if $class=='column_cut'} class="active"{/if}>
									<a href="/misc/column_cut">
										<i class="icon-double-angle-right"></i>
										Cut Data Column
									</a>
								</li>

								<li{if $class=='pad_bedfile'} class="active"{/if}>
									<a href="/misc/pad_bedfile">
										<i class="icon-double-angle-right"></i>
										Pad nucleotides in BED file
									</a>
								</li>

								<li{if $class=='sample'} class="active"{/if}>
									<a href="/misc/sample">
										<i class="icon-double-angle-right"></i>
										Random Sampler
									</a>
								</li>

								<li{if $class=='colour_picker'} class="active"{/if}>
									<a href="/misc/colour_picker">
										<i class="icon-double-angle-right"></i>
										Colour Picker
									</a>
								</li>

{*								<li>
									<a href="profile.html">
										<i class="icon-double-angle-right"></i>
										User Profile
									</a>
								</li>

								<li>
									<a href="inbox.html">
										<i class="icon-double-angle-right"></i>
										Inbox
									</a>
								</li>

								<li>
									<a href="pricing.html">
										<i class="icon-double-angle-right"></i>
										Pricing Tables
									</a>
								</li>

								<li>
									<a href="invoice.html">
										<i class="icon-double-angle-right"></i>
										Invoice
									</a>
								</li>

								<li>
									<a href="timeline.html">
										<i class="icon-double-angle-right"></i>
										Timeline
									</a>
								</li>

								<li>
									<a href="login.html">
										<i class="icon-double-angle-right"></i>
										Login &amp; Register
									</a>
								</li>
*}
							</ul>
						</li>

						<li>
							<a href="https://cran.biotools.fr/" target="_blank">
								<i style="background:url('/images/cran-icon.png') no-repeat;margin-left:5px;"><img src="/images/1pixel.gif" style="border:0; width:25px; height:19px"></i>
								<span class="menu-text"> CRAN Repository </span>
							</a>
						</li>

{*						<li>
							<a href="#" class="dropdown-toggle">
								<i class="icon-desktop"></i>
								<span class="menu-text"> UI Elements </span>

								<b class="arrow icon-angle-down"></b>
							</a>

							<ul class="submenu">
								<li>
									<a href="elements.html">
										<i class="icon-double-angle-right"></i>
										Elements
									</a>
								</li>

								<li>
									<a href="buttons.html">
										<i class="icon-double-angle-right"></i>
										Buttons &amp; Icons
									</a>
								</li>

								<li>
									<a href="treeview.html">
										<i class="icon-double-angle-right"></i>
										Treeview
									</a>
								</li>

								<li>
									<a href="jquery-ui.html">
										<i class="icon-double-angle-right"></i>
										jQuery UI
									</a>
								</li>

								<li>
									<a href="nestable-list.html">
										<i class="icon-double-angle-right"></i>
										Nestable Lists
									</a>
								</li>

								<li>
									<a href="#" class="dropdown-toggle">
										<i class="icon-double-angle-right"></i>

										Three Level Menu
										<b class="arrow icon-angle-down"></b>
									</a>

									<ul class="submenu">
										<li>
											<a href="#">
												<i class="icon-leaf"></i>
												Item #1
											</a>
										</li>

										<li>
											<a href="#" class="dropdown-toggle">
												<i class="icon-pencil"></i>

												4th level
												<b class="arrow icon-angle-down"></b>
											</a>

											<ul class="submenu">
												<li>
													<a href="#">
														<i class="icon-plus"></i>
														Add Product
													</a>
												</li>

												<li>
													<a href="#">
														<i class="icon-eye-open"></i>
														View Products
													</a>
												</li>
											</ul>
										</li>
									</ul>
								</li>
							</ul>
						</li>

						<li>
							<a href="#" class="dropdown-toggle">
								<i class="icon-text-width"></i>
								<span class="menu-text"> Tables </span>

								<b class="arrow icon-angle-down"></b>
							</a>

							<ul class="submenu">
								<li>
									<a href="tables.html">
										<i class="icon-double-angle-right"></i>
										Simple &amp; Dynamic
									</a>
								</li>

								<li>
									<a href="jqgrid.html">
										<i class="icon-double-angle-right"></i>
										jqGrid plugin
									</a>
								</li>
							</ul>
						</li>

						<li>
							<a href="#" class="dropdown-toggle">
								<i class="icon-edit"></i>
								<span class="menu-text"> Forms </span>

								<b class="arrow icon-angle-down"></b>
							</a>

							<ul class="submenu">
								<li>
									<a href="form-elements.html">
										<i class="icon-double-angle-right"></i>
										Form Elements
									</a>
								</li>

								<li>
									<a href="form-wizard.html">
										<i class="icon-double-angle-right"></i>
										Wizard &amp; Validation
									</a>
								</li>

								<li>
									<a href="wysiwyg.html">
										<i class="icon-double-angle-right"></i>
										Wysiwyg &amp; Markdown
									</a>
								</li>

								<li>
									<a href="dropzone.html">
										<i class="icon-double-angle-right"></i>
										Dropzone File Upload
									</a>
								</li>
							</ul>
						</li>

						<li>
							<a href="widgets.html">
								<i class="icon-list-alt"></i>
								<span class="menu-text"> Widgets </span>
							</a>
						</li>

						<li>
							<a href="calendar.html">
								<i class="icon-calendar"></i>

								<span class="menu-text">
									Calendar
									<span class="badge badge-transparent tooltip-error" title="2&nbsp;Important&nbsp;Events">
										<i class="icon-warning-sign red bigger-130"></i>
									</span>
								</span>
							</a>
						</li>

						<li>
							<a href="gallery.html">
								<i class="icon-picture"></i>
								<span class="menu-text"> Gallery </span>
							</a>
						</li>

						<li>
							<a href="#" class="dropdown-toggle">
								<i class="icon-tag"></i>
								<span class="menu-text"> Misc. Tools </span>

								<b class="arrow icon-angle-down"></b>
							</a>

							<ul class="submenu">
								<li>
									<a href="profile.html">
										<i class="icon-double-angle-right"></i>
										User Profile
									</a>
								</li>

								<li>
									<a href="inbox.html">
										<i class="icon-double-angle-right"></i>
										Inbox
									</a>
								</li>

								<li>
									<a href="pricing.html">
										<i class="icon-double-angle-right"></i>
										Pricing Tables
									</a>
								</li>

								<li>
									<a href="invoice.html">
										<i class="icon-double-angle-right"></i>
										Invoice
									</a>
								</li>

								<li>
									<a href="timeline.html">
										<i class="icon-double-angle-right"></i>
										Timeline
									</a>
								</li>

								<li>
									<a href="login.html">
										<i class="icon-double-angle-right"></i>
										Login &amp; Register
									</a>
								</li>
							</ul>
						</li>

						<li>
							<a href="#" class="dropdown-toggle">
								<i class="icon-file-alt"></i>

								<span class="menu-text">
									Other Pages
									<span class="badge badge-primary ">5</span>
								</span>

								<b class="arrow icon-angle-down"></b>
							</a>

							<ul class="submenu">
								<li>
									<a href="faq.html">
										<i class="icon-double-angle-right"></i>
										FAQ
									</a>
								</li>

								<li>
									<a href="error-404.html">
										<i class="icon-double-angle-right"></i>
										Error 404
									</a>
								</li>

								<li>
									<a href="error-500.html">
										<i class="icon-double-angle-right"></i>
										Error 500
									</a>
								</li>

								<li>
									<a href="grid.html">
										<i class="icon-double-angle-right"></i>
										Grid
									</a>
								</li>

								<li>
									<a href="blank.html">
										<i class="icon-double-angle-right"></i>
										Blank Page
									</a>
								</li>
							</ul>
						</li>

*}
					</ul><!-- /.nav-list -->

					<div class="sidebar-collapse" id="sidebar-collapse">
						<i class="icon-double-angle-left" data-icon1="icon-double-angle-left" data-icon2="icon-double-angle-right"></i>
					</div>

					{literal}<script type="text/javascript">
						try{ace.settings.check('sidebar' , 'collapsed')}catch(e){}
					</script>{/literal}
				</div>
