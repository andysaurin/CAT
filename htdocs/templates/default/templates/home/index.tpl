	<div style="padding:20px;">
		<div class="page-header">
			<h1>CAT
				<small>
					<i class="icon-double-angle-right"></i>
					A <b>C</b>hIP <b>A</b>ssociation <b>T</b>ester
				</small>
			</h1>
		</div>
		<div style="padding:10px;">

							<span class="row">
								<p>
									CAT allows you to easily compare your peak-called ChIP peaks with those from most all <a href="/home/drosoCAT" title="Test for Drosophila ChIP Colocalisation"><i>D. melanogaster</i> </a>, <a href="/home/mouseCAT" title="Test for Mouse ChIP Colocalisation">mouse</a>, and <a href="/home/mouseCAT" title="Test for Human ChIP Colocalisation">human</a> modENCODE/ENCODE ChIP peaks.
								</p>

								<div class="row center">
									<h1 class="text green lead bold"><strong>Select the appropriate ChIP Association Tester to use</strong></h1>
									<h4><a href="/home/drosoCAT" title="Test for Drosophila ChIP Colocalisation"><strong>Chip Association of <span class="text red">Drosophila</span> proteins (dm3)</strong></a></h4>
									<h4><a href="/home/mouseCAT" title="Test for Mouse ChIP Colocalisation"><strong>Chip Association of <span class="text red">Mouse</span> proteins (mm9)</strong></a></h4>
									<h4><a href="/home/humanCAT" title="Test for Human ChIP Colocalisation"><strong>Chip Association of <span class="text red">Human</span> proteins (hg19)</strong></a></h4>
									<h4><a href="/home/webCAT" title="Test for general user-supplied ChIP Colocalisation"><strong>Chip Association of other data (not ENCODE)</strong></a></h4>
									<br>
								</div>


								<p id="ChIP_Association_Score">
									Comparison is performed using the <a href="http://www.ncbi.nlm.nih.gov/pubmed/23782611" title="GAT" target="_blank">Genomic Association Tester</a> that has been used to pre-compute the association of all proteins on chromatin used by ENCODE and modENCODE in their ChIP datasets.
								</p>
								<p>
									Peaks that share significantly common genomic binding sites score highly, allowing easy identification of proteins that show similar or identical genomic distributions from those that are distinct.
								</p>

								<span class="row">

									<h4 class="text blue smaller">How are ChIP association scores calculated?</h4>
										<p style="padding-left:50px;">
											Scores are calculated as: <br>

											<span style="padding-left:50px;">
												<span class="text blue">Log<sub>2</sub>( observed overlap / expected overlap )</span><strong> x </strong><span class="text blue">-Log<sub>10</sub>( ><i>p</i>-value )</span><strong> x </strong><span class="text blue">sensitivity coefficient</span>

											</span><br>

											<br>The <span class="text blue"><i>sensitivity coefficient</i></span> is what distinguishes <strong>overlapping</strong> ChIP peaks from <strong>highly similar</strong> ChIP peaks and is determined from the percent of total bases overlapping between the two ChIP tracks being tested.
											<br>If 100% of all bases in ChIP A overlap with 100% of all bases in ChIP B, then sensitivity coefficient will be 4 : log10(% overlap total A bases) x log10(% overlap total B bases) )
											<br>
											<br>For example, if we take the following four ChIP peak BED files, ChIP A, ChIP B, ChIP C, and ChIP D :
											<br><img src="/images/peak_association_score_explanation.gif" alt="Track example to explain how ChIP Peak Association Score is calculated" />
											<br>
											Scoring for <strong>overlapping</strong> ChIP peaks will cluster ChIPs A,B,C since they all overlap highly between themselves. This is likely the case for histone modifying enzymes that deposit broad peak histone marks.
											<br><br>
											Scoring for <strong>highly similar</strong> ChIP peaks will cluster ChIPs A and B since they both colocalise well together (>90% total bases of each overlapping). This would be the case for e.g. colocalising transcription co-factors.
											<br>ChIP C will still have an association score higher with A+B than ChIP D will have with A+B, because the observed overlap of C with A/B is still high. However, since the percent total bases of C is low (e.g. <10%), the sensitivity coefficient will lower the association score relative to the observed between A+B when scoring for highly similar peaks.



										</p>

								</span>
							</span>

							<span class="row">
								You can use this tool to simply extract the pre-computed modENCODE or ENCODE data or input a <a href="http://genome.ucsc.edu/FAQ/FAQformat.html" target="_blank">BED-formatted file</a> of your own ChIP data to compare association with some or all of the ENCODE ChIP datasets.
							</span>

							<div class="row">
								<div class="col-xs-12 col-sm-10">
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

								</div>
							</div>

							<div class="row" style="padding:10px;">
								<br />
								<div class="lead">A note on user-supplied BED files...</div>
								<div>
									<ul>
										<li>
											Uploading your own BED file will allow comparison of association of modENCODE ChIP peaks with peaks/genomic regions of your supplied BED file.
											<ul>
												<li>
													This takes approximately 10 minutes for comparison of 1000 ChIP peaks against 200 other factors, depending on the current server load.
												</li>
											</ul>
										</li>
										<li>
											The supplied BED file is usually a ChIP peak BED file which will compare "all ChIP peaks with all ChIP peaks".
											<br />However, you may wish to test association of modENCODE ChIP peaks with individual genomic regions (e.g. genes).

											<ul>
												<li>
													If this is the case, you should check the option "Individual Genomic Regions", which will compare all the selected modENCODE ChIP peaks with each individual genomic region in the provided BED file.
												</li>
											</ul>
										</li>
										<li>
											Due to the time involved in calculating peak association, uploading your own ChIP peak BED file requires the input of a valid email address to which the results will be sent.
										</li>
										<li>
											User-supplied ChIP peak BED files are limited to 5 ChIP peak BED files (unlimited regions), although if you require individual region testing (e.g. a gene BED file), then only 1 BED file is permitted, with a maximum of {$max_gene_regions} regions.
										</li>
									</ul>
								</div>
							</div>






		</div><br>

			</div>