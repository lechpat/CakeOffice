<?php
namespace CakeOffice\View;

use Cake\Core\Exception\Exception;
use Cake\Event\EventManager;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\Utility\Inflector;
use Cake\View\View;

/**
 * @package  Cake.View
 */
class ExcelView extends View {

/**
 * PHPExcel instance
 * @var PhpExcel
 */
	public $PhpExcel = null;

	private $__filename;

	public $subDir = 'xlsx';

/**
 * Excel format
 *
 * @var string
 */
  public $format;

/**
 * Excel extension
 *
 * @var string
 */
    public $ext;

/**
 * Constructor
 *
 * @param \Cake\Network\Request $request Request instance.
 * @param \Cake\Network\Response $response Response instance.
 * @param \Cake\Event\EventManager $eventManager Event manager instance.
 * @param array $viewOptions View options. See View::$_passedVars for list of
 *   options which get set as class properties.
 *
 * @throws \Cake\Core\Exception\Exception
 */
	public function __construct(
        Request $request = null,
        Response $response = null,
        EventManager $eventManager = null, 
        array $viewOptions = []
    ) {
		parent::__construct($request, $response, $eventManager, $viewOptions);

		if (isset($viewOptions['name']) && $viewOptions['name'] == 'Error') {
			$this->subDir = null;
			$this->layoutPath = null;
			$response->type('html');

			return;
		}
        $this->ext = $request->param('_ext');
        $this->format = ($this->ext == 'xlsx')? 'Excel2007': 'Excel5';
		$this->PhpExcel = new \PHPExcel();
	}

/**
 * [render description]
 * @param  [type] $action [description]
 * @param  [type] $layout [description]
 * @param  [type] $file   [description]
 * @return [type]         [description]
 */
	public function render($action = null, $layout = null, $file = null) {
        $this->layoutPath = 'xlsx';
		$content = parent::render($action, $layout, $file);
		if ($this->response->type() == 'text/html') {
			return $content;
		}

        if(!empty($content)) {
            $file = $this->createTempFile($content);
            $reader = \PHPExcel_IOFactory::createReader('HTML');
            $this->PhpExcel = $reader->load($file);
            $this->deleteTempFile($file);
        }
                    
		$content = $this->__output();
		$this->Blocks->set('content', $content);

		$this->response->download($this->getFilename());

		return $this->Blocks->get('content');
	}

/**
 * Generates the binary excel data
 * @return string
 * @throws CakeException If the excel writer does not exist
 */
	private function __output() {
		ob_start();

		$writer = \PHPExcel_IOFactory::createWriter($this->PhpExcel, $this->format);

		if (!isset($writer)) {
			throw new Exception('Excel writer not found');
		}

		$writer->setPreCalculateFormulas(false);
		$writer->setIncludeCharts(true);
		$writer->save('php://output');

		$output = ob_get_clean();

		return $output;
	}

	public function setFilename($filename) {
		$this->__filename = $filename;
	}

	public function getFilename() {
		if (!empty($this->__filename)) {
			return $this->__filename . '.xlsx';
		}
		return Inflector::slug($this->request->url) . '.xlsx';
	}

/**
 * Create temp file in sys_get_temp_dir with view content.
 *
 * @param string $content The view content
 * @return string The tempfile path.
 */
    private function createTempFile($content)
    {
        $file = tempnam(sys_get_temp_dir(), 'cakeoffice_');
        $fp = fopen($file, "w");
        fwrite($fp, $content);
        fclose($fp);
        return $file;
    }

/**
 * Delete tempfile
 *
 * @param string $file The tempfile path
 * @return void
 */
    private function deleteTempFile($file)
    {
        unlink($file);
    }
}
