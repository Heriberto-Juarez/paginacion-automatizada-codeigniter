# Paginación Automatizada Para Codeigniter | Pagination_Model 
Reemplaza la librería de paginación con este simple modelo de codeigniter que hará el trabajo sucio por ti.


Para los desarrolladores que frecuentemente mostramos el contenido de más de una tabla en la que requerimos un sistema de paginación, el trabajo se vuelve más y más repetitivo.  

Entre los trabajos repetitivos que se pueden presentar a la hora de crear una paginación y de los cuales se encarga el modelo de este repositorío estan:  

* Crear un metodo para cada tabla para obtener los registros de una página
* Obtener el total de registros de una página


NOTA: 
El modelo por ahora funciona con un select * a todas las páginas sin posibilidad de cambiar la consulta de forma dinamica
El objetivo es que también se logre cambiar la consulta de forma dinamica. Ya se tiene un prototipo de esta funcionalidad pero por ahora se sigue trabajando en el con la esperanza de que se terminé de programar pronto.

# Antes de utilizar 

Antes de comenzar a utilizar la librería es necesario contar con los siguiente elementos
* La librería de url incluida
* La lubrería de db incluida
* De preferencia tener activa la carácteristica URI Routing de codeignier
En el caso de no tener la funcionalidad de URI Routing debemos ir a la línea 204 y cambiar $uri_assoc = $this->uri->ruri_to_assoc(); por 
$uri_assoc = $this->uri->uri_to_assoc(); sin la letra r antes de uri
* Si estás utilizando bootstrap 4 no será necesario incluir el archivo css.css, de lo contrario si será necesario llamar al archivo para asegurar que las páginas se muestren de forma adecuada


# Modo de uso
  
  Desde nuestro controlador podemos llamar a nuestro modelo de paginación y llamar a sus metodos más importantes
  ```
  $this->load->model("Pagination_Model");
  $this->Pagination_Model->setTabla("producto"); //si en nuestro archivo Pagination_Model.php no hay una tabla por defecto o no es la que usaremos entonces es importante establecer con la tabla que trabajaremos, en este ejemplo se llama 'producto'
  $this->Pagination_Model->setResultadosPorPagina(5); //Si queremos cambiar los resultados por página es IMPORTANTE hacerlo desde ahora, de lo contrario los calculos que realiza el modelo más adelante podrían causar errores al mostrar las páginas  
  $this->Pagination_Model->pagina_actual_uri(); //es importante llamar este método ya que este obtiene la página actual de forma automática de la url
  $this->Pagination_Model->init(); // Exactamente después de llamar los métodos anteriores es importante inicializar algunos valores con el método init() Aquí se hace la consulta a la base de datos y se calcula el total de resultados y páginas
  $this->Pagination_Model->config['base_url'] = base_url() . 'Admin/productos/pagina/'; //Se configura el url de las páginas de acuerdo a nuestras nececidades. Para este ejemplo se supone que se tiene un controlador de nombre 'Admin' con la página o método 'productos' el cual se muestra con paginación
  /**
  * La configuración de la url se puede hacer en cualquier momento previo a mostrar las páginas ya que este se usa solo hasta ese momento.
  * Hasta este punto ya se tiene la mayor parte del trabajo realizada, ahora sólo hay que enviar la información necesaria a nuestra 
  * vista y mostrar la información.
  * Podemos crear e inicializar la variable data para enviar los datos necesarios a nuestra view
  */

  $data['paginas'] = $this->Pagination_Model->paginas();
  $data['resultados'] = $this->Pagination_Model->resultSet()->get()->result_array();
  
  //Ahora cargamos nuestra vista y le enviamos nuestra información almacenada en $data
  
  $this->load->view('ruta_a_tu_vista/vista', $data);
  
  ```
  Ahora desde nuestra **vista.php** mostramos las páginas y los resultados

  ```
  
  <?php echo $paginas ?> <!--Puedes imprimir las páginas primero arriba de la vista y después al fondo, queda a tu elección-->  
  <!--Para este ejemplo tomado de un proyecto real los resultados se muestran en una tabla pero queda a tu 
    criterio el como muestras los resultados-->
    
    <table class="table table-bordered">
      <tr>
        <th>Nombre</th>
        <th>Descripción</th>
        <th>Imagen</th>
        </tr>
        <?php
        foreach ($data['resultSet'] as $record) {
        ?>
          <tr>
            <td><?= $record['prod_nombre'] ?></td>
            <td><?= $record['prod_desc'] ?></td>
            <td>Vacio por ahora</td>
          </tr>
        <?php
        }
        ?>
    </table>
  <?php echo $paginas ?>
  
  ``` 
  
