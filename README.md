## Disclaimer

At now this lib is under development.

# SwaggerUIGenerator

This lib includes components and Symfony bundle for generate OpenApi specification.

## Install
Via Composer
```` bash
$ composer require php-solution/swagger-ui-gen
````

## Example

See example of Symfony app files on /examples/project

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

## Add Symfony Configuration
````
swagger_ui_gen:
    options_provider:
        defaults:
            - '%kernel.root_dir%/Resources/swagger-doc/defaults.yml'
        files:
            - '%kernel.root_dir%/Resources/swagger-doc/general.yml'
            - '%kernel.root_dir%/Resources/swagger-doc/tags.yml'
            - '%kernel.root_dir%/Resources/swagger-doc/paths.yml'
            - '%kernel.root_dir%/Resources/swagger-doc/security_def.yml'
            - '%kernel.root_dir%/Resources/swagger-doc/definitions.yml'
        folders:
            - '@ProjectAdminBundle/Resources/config/swagger-doc'
````

## Generate file with Swagger Specification
````
php bin\console swagger-gen:generate-spec --path=./web/assets/swagger-spec.json
````
    
## Example of symfony route specification
````YAML
sf_route_paths:
  -
    route: 'admin_list' # symfony route name, required
    tags: ['admin'] # openapi tags, not required, default value: []
    schemes: [] # openapi schemes, not required, default value: []
    response: # use for operapi operation response, not required, default value: []
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
    route: 'admin_post_with_form'
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
    route: 'admin_post_with_form_multiple'
    tags: ['admin']
    request:
      form_class: 'Project\AdminBundle\Form\Type\MultipleAdminType'
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
````

## TODO:
1. Use SF Configuration component as normalizer for DefinitionsBuilder, RoutePaths
2. JMSSerializer Schema builder
3. Tests