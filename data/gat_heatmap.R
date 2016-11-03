#! /usr/bin/Rscript

cmd_args <- commandArgs(TRUE)
title <- cmd_args[1]

options(echo=T)

library("pheatmap")
## if pheatmap is not installed,
## add it on-the-fly as follows:
#source("http://bioconductor.org/biocLite.R")
#biocLite("pheatmap")

basedir <- getwd()
outfile <- paste(basedir, "GAT_heatmap.pdf", sep="/")
infile <- paste(basedir, "GAT_scores.xls", sep="\t", header=T )

data <- read.table("GAT_scores.xls", sep="\t", header=T )
rownames(data) <- data[,1]
data$X <- NULL

if ( nrow(data) > 2 ) {
	cluster_rows <- TRUE
} else {
	cluster_rows <- FALSE
}

if ( ncol(data) > 2 ) {
	cluster_cols <- TRUE
} else {
	cluster_cols <- FALSE
}

max <- max(data,na.rm=T)
min <- min(data,na.rm=T)

#PDF width
if ( ncol(data) > 20 ) {
	width <- (ncol(data) / 3)
} else {
	width <- 10
}
#PDF height
if ( nrow(data) > 20 ) {
	height <- (nrow(data) / 3)
} else {
	height <- 10
}

if ( min >= 0 ) {
    #Zero is v.pale blue+yellow with Red being max
    bk <- c(seq(0,max, length=100));
    hmcols <- colorRampPalette( rev(c(
        "#D41309",
     	"#D73027",
     	"#FC6C58",
     	"#FEE090",
     	"#E0F3F8"

    )) )(length(bk)-1)

	pheatmap(as.matrix(data), cluster_rows=cluster_rows, cluster_cols=cluster_cols, main=title, filename=outfile, width=width, height=height, breaks=bk, color=hmcols)
	quit("no")
}

if ( max <= 0 ) {
    #min is deep blue rising to v.pale blue+yellow at zero
    bk <- c(seq(min,0, length=40));
	pheatmap(as.matrix(data), cluster_rows=cluster_rows, cluster_cols=cluster_cols, main=title, filename=outfile, width=width, height=height, breaks=bk)
	quit("no")
}

#negative scores in the blue color range, positive scores in the red range
min_bk = min/3
if ( min_bk < 0 ) min_bk <- -(min_bk)
max_bk = max/4
if ( max_bk < 0 ) max_bk <- -(max_bk)

bk1 = min
bk2 = min + min_bk
bk3 = bk2 + 1e-12
bk4 = bk3 + min_bk
bk5 = bk4 + 1e-12
bk6 = 0
bk7 = 1e-12
bk8 = bk7 + max_bk
bk9 = bk8 + 1e-12
bk10 = bk9 + max_bk
bk11 = bk10 + 1e-12
bk12 = bk11 + max_bk
bk13 = bk12 + 1e-12
bk14 = max

bk <- c(
	seq(bk1, bk2, length=50),
	seq(bk3, bk4, length=50),
	seq(bk5, bk6, length=50),
	seq(bk7, bk8, length=50),
	seq(bk9, bk10, length=50),
	seq(bk11, bk12, length=50),
	seq(bk13, bk14, length=50)
)

hmcols <- colorRampPalette( rev(c(
	"#D41309",
	"#D73027",
	"#FC6C58",
	"#FEE090",
	"#E0F3F8",
	"#91BFDB",
	"#4DA6DD",
	"#4575B4"
)) )(length(bk)-1)


pheatmap(as.matrix(data), cluster_rows=cluster_rows, cluster_cols=cluster_cols, breaks=bk, color=hmcols, main=title, filename=outfile, width=width, height=height)

quit("no")