<?php

namespace Portal\PdfJSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\VarDumper\VarDumper;

class PdfJSController extends Controller
{
    public function indexAction(Request $request)
    {
//        $file = $request->get('file');
//        VarDumper::dump($file);die();
        return $this->render('PortalPdfJSBundle::viewer.html.twig');
    }
}
