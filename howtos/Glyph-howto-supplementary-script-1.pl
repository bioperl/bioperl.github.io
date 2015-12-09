#!/usr/bin/perl
 
use strict;
use lib "$ENV{HOME}/lib";
 
use Bio::Graphics;
use Bio::SeqFeature::Generic;
use Bio::Location::Split;
use Bio::Location::Simple;

my $bsg = 'Bio::SeqFeature::Generic';
my $bls = 'Bio::Location::Simple';
 
my $span         = $bsg->new(-start=>1,-end=>1000);
my $test_feature = $bsg->new(-start=>100,-end=>700,
                             -display_name=>'Test Feature 1',
                             -source_tag=>'Multiple subfeatures');
my $subfeat = $bsg->new(-start=>100,-end=>400);
$subfeat->add_SeqFeature($bsg->new(-start=>100,-end=>200));
$subfeat->add_SeqFeature($bsg->new(-start=>300,-end=>400));
$test_feature->add_SeqFeature($subfeat);
$test_feature->add_SeqFeature($bsg->new(-start=>500,-end=>600));
$test_feature->add_SeqFeature($bsg->new(-start=>650,-end=>700));
 
my $test2_feature = $bsg->new(-start=>680,-end=>800,
			      -display_name=>'Test Feature 2',
			      -source_tag => 'Single top-level feature');
 
my $test3_feature = $bsg->new(-display_name => 'Test Feature 3',
			      -source_tag   => 'Feature with split location');
my $location = Bio::Location::Split->new();
$location->add_sub_Location($bls->new(-start=>200,-end=>300));
$location->add_sub_Location($bls->new(-start=>400,-end=>450));
$location->add_sub_Location($bls->new(-start=>480,-end=>500));
$test3_feature->location($location);
 
my $panel = Bio::Graphics::Panel->new(-width=>600,-length=>$span->length,
                                      -pad_left=>12,-pad_right=>12);
$panel->add_track($span,-glyph=>'arrow',-double=>1,-tick=>2);
 
$panel->add_track([$test_feature,$test2_feature,$test3_feature],
                  -glyph   => 'multihourglass',
                  -bgcolor => 'orange',
                  -font2color => 'red',
                  -height  => 20,
                  -label   => 1,
                  -description => 1,
		 );
 
print $panel->png;