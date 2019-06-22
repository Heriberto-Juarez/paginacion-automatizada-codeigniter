<?php
defined('BASEPATH') OR exit('No direct script access allowed');

final class Pagination_Model extends CI_Model
{
    public $pagina_actual;
    public $total_resultados;
    public $resultados_por_pagina;
    private $consulta;
    public $offset;
    public $tabla;
    public $campos;
    public $total_paginas;
    public $config;

    public function __construct()
    {

        /**
         * Establece los valores iniciales para nuestro objeto, estos pueden ser modificados a tus gustos y/o necesidades.
         */
        parent::__construct();
        $this->resultados_por_pagina = 5; //resultados por página por defecto
        $this->campos = "*"; //los campos a seleccionar de tu tabla por defecto
        $this->offset = 0; //el offset inicial es cero para la primera página, después se calcula automáticamente
        $this->setPaginaActual(1); // establece la página inicial actual. Al llamar este método también se calcula el offset
        $this->setTabla("nombreDeMiTablaPorDefecto"); //este valor puede ser cambiado desde el método setTabla, de preferencia usar aqui el nombre de la tabla que más usemos
        /*
         * base_url() representará la ruta para los enlaces de las páginas:
         * Ejemplo: Si queremos que nuestro esquema de url luzca así:  www.ejemplo.com/articulos/pagina/1
         * Entonces el valor de nuestra "base_url" será => base_url() . 'articulos/pagina/'
         * Pero estos valores deben ser asignados de preferencia desde el controlador donde llamamos a nuestro modelo
         * Ejemplo: $this->Pagination_Model->config['base_url'] = base_url() . 'articulos/pagina/';
         *
         *
         * paginas_mostradas es la cantidad máxima de páginas que se pueden imprimir, esto con el fin de no imprimir todas las páginas
         * Esta última configuración no está probada al 100%.
         * */
        $this->config = [
            "base_url" => base_url() . 'pagina/',
            "paginas_mostradas" => 16
        ];
    }
    /**
     * @return mixed
     * Devuelve el valor de la página actual
     */
    public function getPaginaActual()
    {
        return $this->pagina_actual;
    }

    /**
     * @param mixed $pagina_actual
     * Establece la página actual y calcula el offset necesario para la consulta
     */
    public function setPaginaActual($pagina_actual): void
    {
        $this->pagina_actual = intval($pagina_actual);
        $this->offset = ($this->pagina_actual - 1) * $this->resultados_por_pagina;
        if($this->offset<0){
            $this->offset = 0;
        }
    }

    /**
     * @return mixed
     * Devuelve el total de resultados obtenidos
     */
    public function getTotalResultados()
    {
        return $this->total_resultados;
    }

    /**
     * @param mixed $total_resultados
     * Establece el total de resultados obtenidos
     */
    public function setTotalResultados($total_resultados): void
    {
        $this->total_resultados = $total_resultados;
    }

    /**
     * @return mixed
     * Devuelve los resultados por página establecidos
     */
    public function getResultadosPorPagina()
    {
        return $this->resultados_por_pagina;
    }

    /**
     * @param mixed $resultados_por_pagina
     * Establece los resultados por página que se desean mostrar
     */
    public function setResultadosPorPagina($resultados_por_pagina): void
    {
        $this->resultados_por_pagina = $resultados_por_pagina;
    }

    /**
     * @return mixed
     * Devuelve la consulta que esta establecida en 'consulta'
     */
    public function getConsulta()
    {
        return $this->consulta;
    }

    /**
     * @param mixed $consulta
     * Establece la consulta que queramos ejecutar
     * Para que este método funcione de forma correcta es necesario establecer nuestra consulta llamando a los campos como parámetro de selección
     * Ejemplo:
     * $this->Pagination_Model->setConsulta($this->db->select($this->Pagination_Model->getCampos())->where...);
     * Otra cosa importante es no llamar el método ->get() al establecer la consulta, de otro modo habrían errores de compatibilidad con el resto del código
     */
    public function setConsulta($consulta): void
    {
        $this->consulta = $consulta;
    }

    /**
     * @return mixed
     * Devuelve el offset que se estableció para nuestra consulta
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param mixed $offset
     * Establece el offset que se usará para nuestra consulta
     */
    public function setOffset($offset): void
    {
        $this->offset = $offset;
    }

    /**
     * @return string
     * Devuelve el valor establecido para nuestra tabla
     */
    public function getTabla(): string
    {
        return $this->tabla;
    }

    /**
     * @param string $tabla
     * Establece el valor de la tabla para nuestro objeto
     */
    public function setTabla(string $tabla): void
    {
        $this->tabla = $tabla;
    }

    /**
     * @return string
     * Devuelve los campos que se seleccionarán al realizar nuestra consulta
     */
    public function getCampos(): string
    {
        return $this->campos;
    }

    /**
     * @param string $campos
     * Establece los campos a seleccionar cuando se realice nuestra consulta
     */
    public function setCampos(string $campos): void
    {
        $this->campos = $campos;
    }

    /*
     * @return mixed
     * Devuelve la consulta que esta establecida para nuestro objeto
     * */
    public function consulta()
    {
        return $this->consulta = $this->db->select($this->campos)->from($this->tabla);
    }

    /*
     * Inicializar nuestro modelo, al calcular los registros totales y las páginas totales generadas
     * */
    public function init()
    {
        $this->campos = "count(*)";
        $this->total_resultados = array_values($this->consulta()->get()->result_array()[0])[0];
        $this->total_paginas = intval(ceil($this->total_resultados / $this->resultados_por_pagina));
    }
    /*
     * Obtiene la página actual a través de la URL
     * */
    public function pagina_actual_uri()
    {
        /*
         * Utilizar uri_to_assoc() en lugar de ruri_to_assoc() cuando no se este utilizando la funcionalidad de cambio de rutas de CodeIgniter
         * */
        $uri_assoc = $this->uri->ruri_to_assoc();
        /*
         * Si en el arreglo que devuelve el método ruri_to_assoc() no se encuentra la clave página
         * se tomará la página 1 por defecto
         * */
        if (array_key_exists("pagina", $uri_assoc)) {
            $p = $uri_assoc['pagina']; //p será la pagina encontrada en la url
            /*
             * Si la página de la url es un valor númerico se tomara como página actual, de lo contrario la página por defecto tomada será la 1
             * */
            if (preg_match("/^[0-9]+$/", $p)) {
                $this->setPaginaActual($p);
            } else {
                $this->setPaginaActual(1);
            }
        } else {
            $this->setPaginaActual(1);
        }
    }
    /*
     * @return mixed result
     * Devuelve el resultado de la consulta realizada sin aplicar ->get() ni ->result_array()
     * para así dejar más posibilidades a la hora de manipular nuestros resultados
     * */
    public function resultSet()
    {
        if ($this->pagina_actual > $this->total_paginas) {
            $this->setPaginaActual($this->total_paginas);
        } else if ($this->pagina_actual < 1) {
            $this->setPaginaActual(1);
        }
        $this->campos = "*";
        return $this->consulta()->limit($this->resultados_por_pagina)->offset($this->offset);
    }
    /*
     * @return $str Devuelve un string con el HTML necesario para la paginación
     * */
    public function paginas()
    {

        $max_paginas_por_lado = ceil(($this->config['paginas_mostradas'] - 2) / 2);
        $str = '';
        if ($this->total_paginas < 2) {
            return '';
        }
        $str .= '<nav aria-label="Page navigation example" class="d-flex">
            <ul class="pagination mx-auto">';
        if ($this->pagina_actual > 1) {
            $str .= '<li class="page-item"><a class="page-link" href="' . $this->config['base_url'] . ($this->pagina_actual - 1) . '"> < </a></li>';
        }
        if ($this->pagina_actual - $max_paginas_por_lado > 1) {
            $str .= '<li class="page-item"><a class="page-link bg-info text-white" href="' . $this->config['base_url'] . '1">1</a></li>';
        }
        //ciclo 1
        for ($i = ($this->pagina_actual - $max_paginas_por_lado); $i < $this->pagina_actual; $i++) {
            if ($i > 0) {
                $str .= '<li class="page-item ' . ($this->pagina_actual == $i ? 'active' : "") . '"><a class="page-link" href="' . $this->config['base_url'] . $i . '">' . $i . '</a>
                        </li>';
            }
        }
        $paginas_mostradas = 0;
        //ciclo 2
        for ($i = $this->pagina_actual; $i <= $this->total_paginas; $i++) {
            if ($paginas_mostradas < $max_paginas_por_lado) {
                $str .= '<li class="page-item ' . ($this->pagina_actual == $i ? 'active' : '') . '"><a class="page-link"
                                                                                             href="' . $this->config['base_url'] . $i . '">' . $i . '</a></li>';
                $paginas_mostradas++;
            }
        }
        if (($this->total_paginas - $this->pagina_actual - $max_paginas_por_lado) >= 0) {
            $str .= '<li class="page-item"><a class="page-link bg-info text-white"
                                             href="' . $this->config['base_url'] . $this->total_paginas . '">' . $this->total_paginas .'</a></li>';
        }
        if ($this->pagina_actual < ($this->total_paginas)) {
            $str .= '
                    <li class="page-item"><a class="page-link" href="' . $this->config['base_url'] . ($this->pagina_actual + 1) . '"> > </a></li>';
        }
        $str .= '</ul></nav>';
        return $str;
    }
}