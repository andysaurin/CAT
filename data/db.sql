--
-- Database: `CAT`
--
CREATE DATABASE IF NOT EXISTS `CAT` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `CAT`;

--
-- Table structure for table `collections`
--

CREATE TABLE IF NOT EXISTS `collections` (
  `id` int(11) NOT NULL,
  `source` enum('modencode','encode') NOT NULL DEFAULT 'modencode',
  `data_type_id` smallint(6) NOT NULL,
  `factor_id` int(11) NOT NULL,
  `species_id` tinyint(4) NOT NULL DEFAULT '1',
  `source_id` varchar(15) NOT NULL,
  `source_name` varchar(255) NOT NULL,
  `tissue_id` int(11) NOT NULL,
  `peculiarity` varchar(255) DEFAULT NULL COMMENT 'RNAi etc',
  `exclude` tinyint(4) NOT NULL DEFAULT '0',
  `url` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `collection_data_types`
--

CREATE TABLE IF NOT EXISTS `collection_data_types` (
  `id` int(11) NOT NULL,
  `data_type` varchar(50) NOT NULL COMMENT 'chip-chip/chip-seq/microarray/etc',
  `data_type_group` varchar(50) NOT NULL COMMENT 'chip/transcriptome/spatial'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `collection_factors`
--

CREATE TABLE IF NOT EXISTS `collection_factors` (
  `id` int(11) NOT NULL,
  `factor_name` varchar(50) NOT NULL,
  `factor_fullname` varchar(255) NOT NULL,
  `factor_group` varchar(50) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `collection_factor_groups`
--

CREATE TABLE IF NOT EXISTS `collection_factor_groups` (
  `id` smallint(6) NOT NULL,
  `name` varchar(255) NOT NULL,
  `abbreviation` varchar(20) NOT NULL,
  `display_order` tinyint(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `collection_files`
--

CREATE TABLE IF NOT EXISTS `collection_files` (
  `id` int(11) NOT NULL,
  `collection_id` int(11) NOT NULL,
  `file_type` varchar(10) NOT NULL DEFAULT 'bed' COMMENT 'bed,bam,etc',
  `filename` varchar(255) NOT NULL,
  `filepath` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `collection_tissues`
--

CREATE TABLE IF NOT EXISTS `collection_tissues` (
  `id` int(11) NOT NULL,
  `species_id` tinyint(4) NOT NULL DEFAULT '1',
  `tissue_name` varchar(50) NOT NULL,
  `abbreviation` varchar(50) DEFAULT NULL,
  `modencode_classification` varchar(255) NOT NULL,
  `cell_line` tinyint(4) NOT NULL DEFAULT '1',
  `primary_cells` tinyint(4) NOT NULL DEFAULT '0',
  `min_age` smallint(6) NOT NULL DEFAULT '0',
  `max_age` smallint(6) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `gat_data`
--

CREATE TABLE IF NOT EXISTS `gat_data` (
  `id` int(11) NOT NULL,
  `collection_id_segments` int(11) NOT NULL COMMENT 'Primary Data ID (--segments)',
  `collection_id_annotations` int(11) NOT NULL COMMENT 'Reference Data ID (--annotations)',
  `annotation` varchar(100) NOT NULL,
  `observed` double NOT NULL,
  `expected` double NOT NULL,
  `CI95low` double NOT NULL,
  `CI95high` double NOT NULL,
  `stddev` double NOT NULL,
  `fold` double NOT NULL,
  `l2fold` double NOT NULL,
  `pvalue` varchar(20) NOT NULL,
  `qvalue` varchar(20) NOT NULL,
  `track_nsegments` int(11) NOT NULL,
  `track_size` bigint(20) NOT NULL,
  `track_density` varchar(20) NOT NULL,
  `annotation_nsegments` int(11) NOT NULL,
  `annotation_size` bigint(20) NOT NULL,
  `annotation_density` varchar(20) NOT NULL,
  `overlap_nsegments` int(11) NOT NULL,
  `overlap_size` bigint(20) NOT NULL,
  `overlap_density` varchar(20) NOT NULL,
  `percent_overlap_nsegments_track` double NOT NULL,
  `percent_overlap_size_track` double NOT NULL,
  `percent_overlap_nsegments_annotation` double NOT NULL,
  `percent_overlap_size_annotation` double NOT NULL,
  `gat_log` text,
  `gat_output` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Output from gat_run.py';

-- --------------------------------------------------------

--
-- Table structure for table `gat_data_noHOT`
--

CREATE TABLE IF NOT EXISTS `gat_data_noHOT` (
  `id` int(11) NOT NULL,
  `collection_id_segments` int(11) NOT NULL COMMENT 'Primary Data ID (--segments)',
  `collection_id_annotations` int(11) NOT NULL COMMENT 'Reference Data ID (--annotations)',
  `annotation` varchar(100) NOT NULL,
  `observed` double NOT NULL,
  `expected` double NOT NULL,
  `CI95low` double NOT NULL,
  `CI95high` double NOT NULL,
  `stddev` double NOT NULL,
  `fold` double NOT NULL,
  `l2fold` double NOT NULL,
  `pvalue` varchar(20) NOT NULL,
  `qvalue` varchar(20) NOT NULL,
  `track_nsegments` int(11) NOT NULL,
  `track_size` bigint(20) NOT NULL,
  `track_density` varchar(20) NOT NULL,
  `annotation_nsegments` int(11) NOT NULL,
  `annotation_size` bigint(20) NOT NULL,
  `annotation_density` varchar(20) NOT NULL,
  `overlap_nsegments` int(11) NOT NULL,
  `overlap_size` bigint(20) NOT NULL,
  `overlap_density` varchar(20) NOT NULL,
  `percent_overlap_nsegments_track` double NOT NULL,
  `percent_overlap_size_track` double NOT NULL,
  `percent_overlap_nsegments_annotation` double NOT NULL,
  `percent_overlap_size_annotation` double NOT NULL,
  `gat_log` text,
  `gat_output` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Output from gat_run.py';

-- --------------------------------------------------------

--
-- Table structure for table `species`
--

CREATE TABLE IF NOT EXISTS `species` (
  `id` tinyint(4) NOT NULL,
  `species` varchar(50) NOT NULL DEFAULT 'D.melanogaster'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `collections`
--
ALTER TABLE `collections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `source` (`source`),
  ADD KEY `factor_id` (`factor_id`),
  ADD KEY `data_type_id` (`data_type_id`),
  ADD KEY `species_id_2` (`species_id`),
  ADD KEY `exclude` (`exclude`),
  ADD KEY `tissue_id` (`tissue_id`);

--
-- Indexes for table `collection_data_types`
--
ALTER TABLE `collection_data_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `collection_factors`
--
ALTER TABLE `collection_factors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `factor_name` (`factor_name`),
  ADD KEY `factor_group` (`factor_group`);

--
-- Indexes for table `collection_factor_groups`
--
ALTER TABLE `collection_factor_groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `abbreviation` (`abbreviation`);

--
-- Indexes for table `collection_files`
--
ALTER TABLE `collection_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `collection_id` (`collection_id`),
  ADD KEY `file_type` (`file_type`);

--
-- Indexes for table `collection_tissues`
--
ALTER TABLE `collection_tissues`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cell_line` (`cell_line`),
  ADD KEY `tissue_name` (`tissue_name`),
  ADD KEY `species_id` (`species_id`),
  ADD KEY `abbreviation` (`abbreviation`);

--
-- Indexes for table `gat_data`
--
ALTER TABLE `gat_data`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `segment_annotation` (`collection_id_segments`,`collection_id_annotations`),
  ADD KEY `collection_id_segments` (`collection_id_segments`),
  ADD KEY `collection_id_annotations` (`collection_id_annotations`),
  ADD KEY `expected` (`expected`);

--
-- Indexes for table `gat_data_noHOT`
--
ALTER TABLE `gat_data_noHOT`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `segment_annotation` (`collection_id_segments`,`collection_id_annotations`),
  ADD KEY `collection_id_segments` (`collection_id_segments`),
  ADD KEY `collection_id_annotations` (`collection_id_annotations`),
  ADD KEY `expected` (`expected`);

--
-- Indexes for table `species`
--
ALTER TABLE `species`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `collections`
--
ALTER TABLE `collections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `collection_data_types`
--
ALTER TABLE `collection_data_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `collection_factors`
--
ALTER TABLE `collection_factors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `collection_factor_groups`
--
ALTER TABLE `collection_factor_groups`
  MODIFY `id` smallint(6) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `collection_files`
--
ALTER TABLE `collection_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `collection_tissues`
--
ALTER TABLE `collection_tissues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `gat_data`
--
ALTER TABLE `gat_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `gat_data_noHOT`
--
ALTER TABLE `gat_data_noHOT`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `species`
--
ALTER TABLE `species`
  MODIFY `id` tinyint(4) NOT NULL AUTO_INCREMENT;
