<?php
/**
 * Created by PhpStorm.
 * User: thomasbuatois
 * Date: 19/12/2017
 * Time: 10:39
 */


class stats_controller{

    public function __construct()
    {
    }

    public function example_page(){
        require_once('View/pages/stats_example.php');
    }

    public function renderpage(){
        if(helper::checkSession(array('Role'))){
            switch ($_SESSION["Role"]){
                case 'admin':
                    require_once('View/pages/statistiques_admin.php');
            }
        }
    }

}



class ChartJS
{
    /**
     * @var array chart data
     */
    protected $_datasets = array();

    /**
     * @var array chart labels
     */
    protected $_labels = array();

    /**
     * The chart type
     * @var string
     */
    protected $_type = '';

    /**
     * @var array Specific options for chart
     */
    protected $_options = array();

    /**
     * @var array Canvas html attributes
     */
    protected $_attributes = array();

    /**
     * Add a set of data
     * @param array $data
     * @param array $colors
     * @param string $legend
     * @param array $advanced
     * @param null $name Name can be use to change data later
     */
    public function addDataset($dataset, $name = null)
    {
        if (!$name) {
            $name = count($this->_datasets);
        }
        $this->_datasets[$name] = $dataset;
    }

    public function __construct($type = 'line', $labels = array(), $options = array(), $attributes = array())
    {
        $this->_type = $type;
        $this->_labels = $labels;
        $this->_options = $options;

        // Always save otherAttributes as array
        if ($attributes && !is_array($attributes)) {
            $attributes = array($attributes);
        }

        $this->_attributes = $attributes;
    }

    /**
     * This method allows to echo ChartJS object and directly renders canvas (instead of using ChartJS->render())
     */
    public function __toString()
    {
        return $this->renderCanvas();
    }

    public function renderCanvas()
    {
        $data = $this->_renderData();
        $options = $this->_renderOptions();

        $attributes = $this->_renderAttributes();

        $canvas = "<canvas$attributes data-chartjs=\"" . $this->_type . "\" $data $options></canvas>";

        return $canvas;
    }

    /**
     * Prepare canvas' attributes
     * @return string
     */
    protected function _renderAttributes()
    {
        $attributes = '';
        if (!isset($this->_attributes['id'])) {
            $this->_attributes['id'] = uniqid('chartjs_', true);
        }

        foreach ($this->_attributes as $attribute => $value) {
            $attributes .= ' ' . $attribute . '="' . $value . '"';
        }

        return $attributes;
    }

    /**
     * Render custom options for the chart
     * @return string
     */
    protected function _renderOptions()
    {
        if (empty($this->_options)) {
            return ' data-options=\'null\'';
        }
        return ' data-options=\'' . json_encode($this->_options) . '\'';
    }

    /**
     * Prepare data (labels and dataset) for the chart
     * @return string
     */
    protected function _renderData()
    {
        $array_data = array('labels' => $this->_labels, 'datasets' => $this->_datasets);

        return ' data-data=\'' . json_encode($array_data) . '\'';
    }
}


?>