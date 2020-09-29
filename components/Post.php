<?php namespace Wiz\Blog\Components;

Use Mail;
Use Input;
Use Request;
Use Validator;
use Cms\Classes\ComponentBase;
use Cms\Classes\Theme;
use Wiz\Blog\Models\Post as PostModel;
use Wiz\Blog\Models\Comment as CommnetModel;

class Post extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'Entrada',
            'description' => 'Muestra una entrada.'
        ];
    }

    public function defineProperties()
    {
        return [
            'slug' => [
                'title'       => 'Identificador de la enrtrada',
                'description' => 'Se buscará la entrada utilizando el identificador',
                'default'     => '{{ :slug }}',
                'type'        => 'string',
            ]
        ];
    }

    public function onRun()
    {
        $this->post = $this->page['post'] = $this->loadPost();
        $this->featuredPosts = $this->page['featuredPosts'] = $this->loadFeaturedPosts();
    }

    protected function loadPost()
    {
        $slug = $this->property('slug');
        $post = PostModel::where('slug', $slug)
            ->first();
        return $post;
    }

    protected function loadFeaturedPosts()
    {
        $featuredPosts = PostModel::published()
            ->featured()
            ->orderBy('published_at', 'desc')
            ->take(4)
            ->get();
        return $featuredPosts;
    }

    public function onComment()
    {
        $values = Input::all();
        $rules = [
            'comment_name' => 'required',
            'comment_email' => 'required|email',
            'comment_content' => 'required',
        ];
        $messages = [
            'comment_name.required' => 'Porfavor ingresa tu nombre completo',
            'comment_email.required' => 'Porfavor ingresa tu correo.',
            'comment_email.email' => 'El correo electrónico ingresado no es válido.',
            'comment_content.required' => 'Porfavor ingresa tu comentario.'
        ];
        $validator = Validator::make(Input::all(), $rules, $messages);
        if ($validator->fails()) {
            throw new \ValidationException($validator);
        } 
        else {
            $CommentPost = new CommnetModel();
            $CommentPost->name = $values['comment_name'];
            $CommentPost->email = $values['comment_email'];
            $CommentPost->content = $values['comment_content'];
            $CommentPost->save();

            $slug = $this->property('slug');
            $post = PostModel::where('slug', $slug)
                ->first();

            $post->comments()->add($CommentPost);

            $url = Request::url();
            $vars = [
                'name' => $values['comment_name'],
                'email' => $values['comment_email'],
                'content_message' => $values['comment_content'],
                'url' => $url,
                'message_timestamp' => date('d/m/Y \a \l\a\s H:i:s'),
            ];
            $email = Theme::getEditTheme()->email;
            Mail::send('wiz.blog::mail.commentsform', $vars, function($message) use ($email) {
                $message->to($email);
            });
            return [
                '#commentForm' => $this->renderPartial('blog/comment_response')
            ];
        }
    }

}
