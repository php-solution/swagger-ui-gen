## Disclaimer

At now this lib is under development.

# SwaggerUIGenerator

This lib includes components and Symfony bundle for generate OpenApi specification.

## Install
Via Composer
```` bash
$ composer require php-solution/swagger-ui-gen
````

## Integration to Project

1. Copy to web document root [swagger-ui dist files](https://github.com/swagger-api/swagger-ui/tree/master/dist)

2. Copy to /web/assets/swagger
    - index.html
    - swagger-ui.css
    - swagger-ui.js
    - swagger-ui-bundle.js
    - swagger-ui-standalone-preset.js
    
3. Change on index.html url to swagger openapi specification
````
const ui = SwaggerUIBundle({
    url: "http://localhost/assets/swagger-spec.json",
})
````
4. Add line to config/bundles.php
````
PhpSolution\SwaggerUIGen\Bundle\SwaggerUIGenBundle::class => ['all' => true],
````    

## Add Symfony Configuration
Create file :
 
    touch config/packages/swagger_uigen.yaml
    
Add the following configuration to the recently created file:
    
````
swagger_ui_gen:
    options_provider:
        defaults:
            - '%kernel.project_dir%/Resources/swagger-doc/defaults.yml'
        files:
            - '%kernel.project_dir%/Resources/swagger-doc/general.yml'
            - '%kernel.project_dir%/Resources/swagger-doc/tags.yml'
            - '%kernel.project_dir%/Resources/swagger-doc/paths.yml'
            - '%kernel.project_dir%/Resources/swagger-doc/security_def.yml'
            - '%kernel.project_dir%/Resources/swagger-doc/definitions.yml'
        folders:
            - '@ProjectAdminBundle/Resources/config/swagger-doc'
    handlers:
        validator: false
        form: false
        form_validator: false
        serializer: false
        doctrine_orm: false
    naming_strategy_service: 'PhpSolution\SwaggerUIGen\Bundle\ModelHandler\PropertyNaming\UnderscoreNamingStrategy'
````

## Generate file with Swagger Specification
````
php bin/console swagger-gen:generate-spec --path=./web/assets/swagger-spec.json
````

## Examples

#### Symfony project
See example of Symfony app files on /examples/project.

#### Debug mode for Symfony
 
1. Add route to your general route.yml
````YAML
_swagger_ui_gen:
    resource: '@SwaggerUIGenBundle/Resources/config/routing.yml'
````
2. Add to swagger ui bootstrap html file(index.html):
````
<script>
window.onload = function() {
function getUrlParameter(name) {
      name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
      var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
      var results = regex.exec(location.search);
      return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
  }
  const debugDataUrl = getUrlParameter('url');
  const ui = SwaggerUIBundle({
    url: debugDataUrl ? debugDataUrl : "./data.json",
    
    ...
</script>
````
3. Use on browser:
````
http://localhost/path-to-swagger-ui-bootstrap-html.html?url=/swagger-ui-gen/data.json
````

4. See dumped data
````
http://localhost/swagger-ui-gen/dump
````
    
## Example of symfony route specification
````YAML
sf_route_paths:
  -
    route: 'get_list_of_entity' # symfony route name, required
    tags: ['admin'] # openapi tags, not required, default value: []
    schemes: [] # openapi schemes, not required, default value: []
    response: # use for openapi operation response, not required, default value: []
      status_code: 200 # not required, default value: "default"
      type: 'object' # not required, default value: "object", complex values: ['array', 'object', 'collection']
      mapping:
        type: ['doctrine', 'serializer'] # not required, use all if builders empty
        class: 'Project\AdminBundle\Lib\PaginatedCollection' # required
        serializer_groups: ['list'] # use only for serializer
      properties:
        items: # property name, require
          type: 'collection'
          mapping: {class: 'Project\AdminBundle\Entity\Admin'}
    openapi_params: # not required, default value: []
      get:
        summary: 'Get admin list'
        responses:
          << : *defaultResponceErrors
        security:
          - api_key: []
  -
    route: 'post_with_form_dto'
    tags: ['admin']
    request:
      form_class: 'Project\AdminBundle\Form\Type\AdminCreateType'
      form_options: {validation_groups: ['create']}
      validation_class: 'Project\AdminBundle\Lib\AdminModel'
      validation_groups: ['create']
    response:
      type: 'object'
      mapping:
        type: ['doctrine', 'serializer']
        class: 'Project\AdminBundle\Entity\Admin'
        serializer_groups: ['get']
  -
    route: 'post_with_form_multiple_dto'
    tags: ['admin']
    request:
      form_class: 'Project\AdminBundle\Form\Type\MultipleAdminType'
      in: 'body' # use this option if you want to send data as json
    response:
      type: 'collection'
      openapi_params: {$ref: '#/definitions/Admin'}
````

## Example of definition specification
````YAML
sf_object_definitions:
  - name: 'Admin'
    mapping:
      type: ['doctrine', 'serializer']
      class: 'Project\AdminBundle\Entity\Admin'
  - name: 'AdminCreate'
    type: 'object'
    mapping:
      type: ['validator']
      validation_groups: ['create']
      class: 'Project\AdminBundle\Lib\AdminListModel' # Some DTO
    properties:
      admins:
        type: 'collection'
        mapping: {class: 'Project\AdminBundle\Lib\AdminModel'}
    openapi_params:
      properties:
        email: {uniqueItems: true}
````

## TODO:
1. Use SF Configuration component as normalizer for DefinitionsBuilder, RoutePaths
2. JMSSerializer Schema builder
3. Tests
