# Blog
OctoberCMS plugin to manage general blog posts.

Developed by Wiz Comunicaciones.




Theme integration
=======================================

Basic inclusion
----------------------------
1. On the theme page php block, include the posts models
```php
<?php
use Wiz\Blog\Models\Post;

function onStart() {    
    $this['items'] = Post::published()
        ->orderBy('published_at', 'desc')
        ->get();
}
?>
```
2. Use in twig templates
```twig
{% for item in items %}
    {{ item.name }}
    .
    .
    .
{% endfor %}
```
3. Use `dump` inside the for loop to review the post object
```twig
{{ dump(item) }}
```
