<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Stream;
use \Firebase\JWT\JWT;


function pathFile($file){
    $trocea = explode(DIRECTORY_SEPARATOR, __DIR__);
    if(strrpos($file, '.html')) $file = 'content'.DIRECTORY_SEPARATOR.$file;
    $trocea[count($trocea)-1] = 'data'.DIRECTORY_SEPARATOR.$file;
    $path = implode(DIRECTORY_SEPARATOR,$trocea);
    return $path;
}

return function (App $app) {
    $container = $app->getContainer();

    // login de acceso a la api
    $app->post('/api-login', function (Request $request, Response $response, array $args) { 
        $settings = $this->get('settings'); 
        $input = $request->getParsedBody();
        if($input['username'] != $settings['admin']['username'] || $input['password'] != $settings['admin']['password']) {
            return $this->response->withJson(['error' => true, 'message' => 'These credentials do not match our records.']);
        }                
        $token = JWT::encode(['valid' => true], $settings['jwt']['secret'], "HS256");     
        return $this->response->withJson(['error' => false, 'token' => $token]);     
    });

    $app->group('/api', function(\Slim\App $app) { 
        $app->get('/validateToken',function(Request $request, Response $response, array $args) {
            print_r($request->getAttribute('decoded_token_data'));
        });
        
        $app->get('/templates', function (Request $request, Response $response, array $args) use ($container) {
            $templates = array_diff(scandir('../templates'), array('..', '.', 'includes', 'sitemap.xml')); 
            return $this->response->withJson($templates);   
         });

        $app->get('/[{file}]',function(Request $request, Response $response, array $args) {
            $path = pathFile($args['file']);
            if($path != null){
                $fh = fopen($path, 'rb');
                $file_stream = new Stream($fh);
                return $response->withBody($file_stream)
                    ->withHeader('Content-Disposition', 'attachment; filename='.$args['file'].';')
                    ->withHeader('Content-Type', mime_content_type($path))
                    ->withHeader('Content-Length', filesize($path));               
            }                
        });

        $app->post('/[{file}]',function(Request $request, Response $response, array $args) {
            $path = pathFile($args['file']);
            $input = $request->getParsedBody();
            if($path != null){
                file_put_contents($path, $input['data']);   
                return $this->response->withJson(['result' => true]);               
            }  
            return $this->response->withJson(['result' => false]);                       
        });    

        $app->delete('/[{file}]',function(Request $request, Response $response, array $args) {
            $path = pathFile($args['file']);
            if(file_exists($path)){
                unlink($path);
            }
            return $this->response->withJson(['result' => true]);                                   
        });

    });    


    $app->get('/sitemap.xml', function (Request $request, Response $response, array $args) use ($container) { 
        $settings = $this->get('settings'); 
        $rutas = json_decode(file_get_contents(pathFile('rutas.json')));        
        $trocea = explode(DIRECTORY_SEPARATOR, __DIR__);
        $trocea[count($trocea)-1] = 'templates';
        $path = implode(DIRECTORY_SEPARATOR, $trocea);
        return $container->get('renderer')
                         ->render($response, 'sitemap.xml', ['path'=>$path, 'rutas' => $rutas, 'basePath'=>$settings['basepath']])
                         ->withHeader('Content-Type', 'text/xml');
    });     


    $app->get('/[{name:.*}]', function (Request $request, Response $response, array $args) use ($container) {      
        $path = (array_key_exists('name', $args))?('/' . $args['name']):'/';

        $rutas = json_decode(file_get_contents(pathFile('rutas.json')), true);
        if(array_key_exists($path, $rutas)){
            $contentPath = pathFile($rutas[$path]['content']);
            if(file_exists($contentPath)){
                $rutas[$path]['params']['content'] = file_get_contents($contentPath);
            }
            return $container->get('renderer')->render($response, $rutas[$path]['template'], $rutas[$path]['params']);
        }else{
            $redireciones = json_decode(file_get_contents(pathFile('redirecciones.json')), true);
            if(array_key_exists($path, $redireciones)){
                header('Location: ' . $redireciones[$path]);
                die();                
            }else{
                echo $path;
            }
        }
    });

};
