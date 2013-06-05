<?php
class Sitemap extends CI_Controller {

	function sitemap()
	{
		$this->load->helper('file');
		$sections=$this->db->get('nabaki_sections');
		
		$this->db->order_by('section');
		$categories=$this->db->get('nabaki_categories');
		
		$this->db->order_by('category');
		$products=$this->db->get('nabaki_products');
		
		$data['html'] = '';
	
		foreach ($sections->result() as $section)
		{
			$data['html'] .= '<h1>' . $section->title . '</h1>';
			
			
			$this->db->where('section',$section->id);
			$categories=$this->db->get('nabaki_categories');
			
			foreach($categories->result() as $category)
			{
				$data['html'] .= '<h2><a title = "' . $category->title . '" href = "' . base_url() . 'category/' . $category->url . '">' . $category->title . '</a></h2>';
				
				$this->db->where('category',$category->id);
				$products=$this->db->get('nabaki_products');
				
				$data['html'] .= '<ul>';
				foreach($products->result() as $product)
				{
					$data['html'] .= '<li><a title = "' . $product->title . '" href = "' . base_url() . 'product/' . $product->id . '">' . $product->title . '</a></li>';
					
				}
				$data['html'] .= '</ul>';
			}
			
		}
		
		$data['html'] .= '<h1><a href="' . base_url() . 'technicians">Nabaki Trained Technicians</a></h1>';
		$data['html'] .= '<h1><a href="' . base_url() . 'about-nabaki">About Nabaki</a></h1>';
		$data['html'] .= '<h1><a href="' . base_url() . 'contacts-locations">Contact Us</a></h1>';

		

		
	}
	
	function xml_sitemap()
	{
		$this->load->helper('file');
		$sections=$this->db->get('nabaki_sections');
		
		$this->db->order_by('section');
		$categories=$this->db->get('nabaki_categories');
		
		$this->db->order_by('category');
		$products=$this->db->get('nabaki_products');
	
		

		
		$xml = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		
		



		$xml .= '<url><loc>' . base_url() . '</loc><lastmod>' . date('Y-m-d') . '</lastmod><changefreq>weekly</changefreq><priority>1</priority></url>';

		
		
		foreach ($sections->result() as $section)
		{
			
			$this->db->where('section',$section->id);
			$categories=$this->db->get('nabaki_categories');
			
			foreach($categories->result() as $category)
			{
				
				$xml .= '<url><loc>' . base_url() . 'category/' . $category->url . '</loc><lastmod>' . date('Y-m-d') . '</lastmod><changefreq>weekly</changefreq><priority>1</priority></url>';
				$this->db->where('category',$category->id);
				$products=$this->db->get('nabaki_products');
				
				foreach($products->result() as $product)
				{
					$xml .= '<url><loc>' . base_url() . 'product/' . $product->id . '</loc><lastmod>' . date('Y-m-d') . '</lastmod><changefreq>weekly</changefreq><priority>1</priority></url>';
				}

			}
			
		}
		
		$xml .= '<url><loc>' . base_url() . 'about-nabaki</loc><lastmod>' . date('Y-m-d') . '</lastmod><changefreq>weekly</changefreq><priority>1</priority></url>';
		$xml .= '<url><loc>' . base_url() . 'technicians</loc><lastmod>' . date('Y-m-d') . '</lastmod><changefreq>weekly</changefreq><priority>1</priority></url>';
		$xml .= '<url><loc>' . base_url() . 'contacts-locations</loc><lastmod>' . date('Y-m-d') . '</lastmod><changefreq>weekly</changefreq><priority>1</priority></url>';
		
		
		
		$xml .= '</urlset>';
		
		if ( ! write_file('sitemap.xml', $xml))
		{
			 echo 'Unable to write the xml file';
		}
		else
		{
			echo 'xml Sitemap was updated';
		}
	}
	
}