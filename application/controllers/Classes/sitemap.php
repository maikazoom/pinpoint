<?php
class Sitemap extends CI_Controller {

	function index()
	{
		$this->load->helper('file');
		$destinations=$this->db->get('znz_destinations');
		$day_trips=$this->db->get('znz_day_trips');
		$overnight_packages=$this->db->get('znz_overnight_packages');
		
		$html = '<?php get_header();   /* Template Name: Sitemap */ ?><div style = "margin-left: 50px;">';
		$html_historical_destinations = '';
		$html_beaches = '';
		$html_activities = '';
		$html_day_trips = '';
		$html_overnight_packages = '';
		$html_slavery = '';
		$html_contact_us = '<h1><a href="http://www.zanzibarpackage.com/contacts-bookings">Contacts & Bookings</a></h1>';
		$html_custom_packages = '<h1><a href="http://www.zanzibarpackage.com/custom-packages">Custom Packages</a></h1>';	
		
		$xml = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		
		



		$xml .= '<url><loc>http://www.zanzibarpackage.com/</loc><lastmod>' . date('Y-m-d') . '</lastmod><changefreq>weekly</changefreq><priority>1</priority></url>';


		$xml_historical_destinations = '';
		$xml_beaches = '';
		$xml_activities = '';
		$xml_day_trips = '';
		$xml_overnight_packages = '';
		$xml_slavery = '';
		$xml_contact_us = '<url><loc>http://www.zanzibarpackage.com/contacts-bookings</loc><lastmod>' . date('Y-m-d') . '</lastmod><changefreq>weekly</changefreq><priority>1</priority></url>';
		$xml_custom_packages = '<url><loc>http://www.zanzibarpackage.com/custom-packages</loc><lastmod>' . date('Y-m-d') . '</lastmod><changefreq>weekly</changefreq><priority>1</priority></url>';
		
		$html .= '<h1>Destinations</h1>';
		$html_historical_destinations = '<ul style = "list-style-type: none;"><li><a href = "http://www.zanzibarpackage.com/destinations/historical-sizes-in-zanzibar/">Historical Sites in Zanzibar</a></li><ul style = "list-style-type: none;">';
		$xml_historical_destinations .= '<url><loc>http://www.zanzibarpackage.com/destinations/historical-sizes-in-zanzibar</loc><lastmod>' . date('Y-m-d') . '</lastmod><changefreq>weekly</changefreq><priority>0.8</priority></url>';
		
		$html_activities = '<ul style = "list-style-type: none;"><li><a href = "http://www.zanzibarpackage.com/destinations/activities-zanzibar">Activities & Things to do in Zanzibar</a></li><ul style = "list-style-type: none;">';
		$xml_activities .= '<url><loc>http://www.zanzibarpackage.com/destinations/activities-zanzibar</loc><lastmod>' . date('Y-m-d') . '</lastmod><changefreq>weekly</changefreq><priority>0.8</priority></url>';
		
		
		$html_beaches = '<ul style = "list-style-type: none;"><li><a href = "http://www.zanzibarpackage.com/destinations/zanzibars-white-sandy-beaches">Zanzibars White Sandy Beaches</a></li><ul style = "list-style-type: none;">';
		$xml_beaches .= '<url><loc>http://www.zanzibarpackage.com/destinations/zanzibars-white-sandy-beaches</loc><lastmod>' . date('Y-m-d') . '</lastmod><changefreq>weekly</changefreq><priority>0.8</priority></url>';
		
		foreach ($destinations->result() as $destination)
		{
			switch($destination->type)
			{
				case 1:
				$html_historical_destinations .= '<li><a title = "' . $destination->title . '" href = "http://www.zanzibarpackage.com/destinations/historical-sizes-in-zanzibar#' . $destination->anchor_name . '">' . $destination->title . '</a></li>';
				$xml_historical_destinations .= '<url><loc>http://www.zanzibarpackage.com/destinations/historical-sizes-in-zanzibar#' . $destination->anchor_name . '</loc><lastmod>' . date('Y-m-d') . '</lastmod><changefreq>weekly</changefreq><priority>0.8</priority></url>';
				break;				
				
				case 2:
				$html_activities .= '<li><a title = "' . $destination->title . '" href = "http://www.zanzibarpackage.com/destinations/activities-zanzibar#' . $destination->anchor_name . '">' . $destination->title . '</a>';
				$xml_activities .= '<url><loc>http://www.zanzibarpackage.com/destinations/activities-zanzibar#' . $destination->anchor_name . '</loc><lastmod>' . date('Y-m-d') . '</lastmod><changefreq>weekly</changefreq><priority>0.8</priority></url>';
				break;				
				
				case 3:
				$html_beaches .= '<li><a title = "' . $destination->title . '" href = "http://www.zanzibarpackage.com/destinations/zanzibars-white-sandy-beaches#' . $destination->anchor_name . '">' . $destination->title . '</a>';
				$xml_activities .= '<url><loc>http://www.zanzibarpackage.com/destinations/zanzibars-white-sandy-beachesr#' . $destination->anchor_name . '</loc><lastmod>' . date('Y-m-d') . '</lastmod><changefreq>weekly</changefreq><priority>0.8</priority></url>';
				break;
			}
		}
		
		$html_historical_destinations .= '</ul></ul><br>';
		$html_activities .= '</ul></ul><br>';
		$html_beaches .= '</ul></ul><br>';
		
		$html .= $html_historical_destinations;
		$xml .= $xml_historical_destinations;
		$html .= $html_activities;
		$xml .= $xml_activities;
		$html .= $html_beaches;
		$xml .= $xml_beaches;
		
		$html_day_trips .= '<h1><a href="http://www.zanzibarpackage.com/day-trips">Zanzibar Day Trips</a></h1><ul style = "list-style-type: none;">';
		$xml_day_trips .= '<url><loc>http://www.zanzibarpackage.com/day-trips</loc><lastmod>' . date('Y-m-d') . '</lastmod><changefreq>weekly</changefreq><priority>0.8</priority></url>';		
		
		
		$html_slavery .= '<h1><a href="http://www.zanzibarpackage.com/slavery-zanzibar">Slavery in Zanzibar</a></h1><ul style = "list-style-type: none;">';
		$xml_day_trips .= '<url><loc>http://www.zanzibarpackage.com/slavery-zanzibar</loc><lastmod>' . date('Y-m-d') . '</lastmod><changefreq>weekly</changefreq><priority>0.8</priority></url>';
		
		foreach ($day_trips->result() as $day_trip)
		{
			$html_day_trips .= '<li><a href="http://www.zanzibarpackage.com/details?package_id=' . $day_trip->id . '">' . $day_trip->title . '</a></li>';
			$xml_day_trips .= '<url><loc>http://www.zanzibarpackage.com/details?package_id=' . $day_trip->id . '</loc><lastmod>' . date('Y-m-d') . '</lastmod><changefreq>weekly</changefreq><priority>0.8</priority></url>';
			if($day_trip->slave_tour == 1)
			{
				$html_slavery  .= '<li><a href="http://www.zanzibarpackage.com/details?package_id=' . $day_trip->id . '">' . $day_trip->title . '</a></li>';
				$xml_slavery .= '<url><loc>http://www.zanzibarpackage.com/details?package_id=' . $day_trip->id . '</loc><lastmod>' . date('Y-m-d') . '</lastmod><changefreq>weekly</changefreq><priority>0.8</priority></url>';
			}
		}
		
		$html_day_trips .= '</ul><br>';
		$html .= $html_day_trips;
		$xml .= $xml_day_trips;
		
		
		$html_overnight_packages .= '<h1><a href="http://www.zanzibarpackage.com/overnight-packages">Overnight Packages</a></h1><ul style = "list-style-type: none;">';
		
		foreach ($overnight_packages->result() as $overnight)
		{
			$html_overnight_packages .= '<li><a href="http://www.zanzibarpackage.com/details?night_package_id=' . $overnight->id . '">' . $overnight->title . '</a></li>';
			$xml_overnight_packages .= '<url><loc>http://www.zanzibarpackage.com/details?package_id=' . $overnight->id . '</loc><lastmod>' . date('Y-m-d') . '</lastmod><changefreq>weekly</changefreq><priority>0.8</priority></url>';
			if($overnight->slave_tour == 1)
			{
				$html_slavery  .= '<li><a href="http://www.zanzibarpackage.com/details?night_package_id=' . $overnight->id . '">' . $overnight->title . '</a></li>';
				$xml_slavery .= '<url><loc>http://www.zanzibarpackage.com/details?night_package_id=' . $overnight->id . '</loc><lastmod>' . date('Y-m-d') . '</lastmod><changefreq>weekly</changefreq><priority>0.8</priority></url>';
			}
		}
		
		$html_slavery .= '</ul>';
		$html_overnight_packages .= '</ul><br>';
		$html .= $html_overnight_packages;
		$xml .= $xml_overnight_packages;
		$html .= $html_slavery;
		$xml .= $xml_slavery;
		$html .= "<br>" . $html_contact_us;
		$html .= "<br>" . $html_custom_packages;
		
		$html .= '<?php get_footer(); ?></div>';
		
		$xml .= '</urlset>';
		
		if ( ! write_file('../sitemap.xml', $xml))
		{
			 //echo 'Unable to write the xml file';
		}
		else
		{
			//echo 'hehehehe xml';
		}
		
		if ( ! write_file('../wp-content/themes/twentyeleven/sitemap.php', $html))
		{
			// echo 'Unable to write the php file';
		}
		else
		{
			//echo 'hehehehe html';
		}

		
	}
	
}