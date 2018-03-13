<?php

namespace xavoc\ispmanager;

class page_cafprint extends \Page{

	function init(){
		parent::init();

		$cid = $this->app->stickyGET('contact_id');
		$contact_model = $this->add('xepan\base\Model_Contact')->load($cid);

		$caf_view = $this->add('xavoc\ispmanager\View_CAFPrint');

		$pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('xEpan ERP');
		$pdf->SetTitle('CAF of '.$contact_model['name']);
		$pdf->SetSubject('CAF of '.$contact_model['name']);
		$pdf->SetKeywords('CAF of '.$contact_model['name']);

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		// set font
		$pdf->SetFont('dejavusans', '', 10);
		//remove header or footer hr lines
		$pdf->SetPrintHeader(false);
		$pdf->SetPrintFooter(false);
		// add a page
		$pdf->AddPage();
		$html = $caf_view->getHTML();
		// echo "string".$html;
		// echo $html;
		// exit;
		// output the HTML content
		$pdf->writeHTML($html, false, false, true, false, '');
		// set default form properties
		$pdf->setFormDefaultProp(array('lineWidth'=>1, 'borderStyle'=>'solid', 'fillColor'=>array(255, 255, 200), 'strokeColor'=>array(255, 128, 128)));
		// reset pointer to the last page
		$pdf->lastPage();
		//Close and output PDF document
		// return;
		// return $pdf->Output(null, 'S');
		// dump
		return $pdf->Output(null, 'I');
	}
}