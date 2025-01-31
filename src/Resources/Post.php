<?php

namespace Mattmangoni\NovaBlogifyTool\Resources;

use App\Nova\User;
use App\Nova\Resource;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Markdown;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\BelongsTo;
use Froala\NovaFroalaField\Froala;
use Laravel\Nova\Fields\BelongsToMany;
use Mattmangoni\NovaBlogifyTool\Models\Image;
use Mattmangoni\NovaBlogifyTool\Fields\ImageUpload;
use Mattmangoni\NovaBlogifyTool\Processors\StoreImage;
use Mattmangoni\NovaBlogifyTool\Metrics\Posts\NewPosts;
use Mattmangoni\NovaBlogifyTool\Metrics\Posts\PostsTrend;

class Post extends Resource
{
    /**
     * The model the resource corresponds to.
     * @var string
     */
    public static $model = 'Mattmangoni\NovaBlogifyTool\Models\Post';

    /**
     * The single value that should be used to represent the resource when being displayed.
     * @var string
     */
    public static $title = 'title';

    /**
     * Hide resource from Nova's standard menu.
     * @var bool
     */
    public static $displayInNavigation = false;

    /**
     * Get the searchable columns for the resource.
     * @return array
     */
    public static function searchableColumns()
    {
        return config('nova-blogify.resources.posts.search');
    }

    /**
     * Get the fields displayed by the resource.
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('Author', 'author', User::class)
                ->sortable()
                ->rules(['required']),

            ImageUpload::make('Image', 'image', Image::class)
                ->store(new StoreImage)
                ->onlyOnForms(),

            BelongsTo::make('Category', 'category', Category::class)
                ->sortable()
                ->rules(['required']),

            HasMany::make('Comments', 'comments', Comment::class)
                ->sortable()
                ->rules(['required']),

            Text::make('Title')
                ->sortable()
                ->rules(['required']),

            Froala::make('Summary')->hideFromIndex(),

            Froala::make('Body')->rules(['required', 'string']),

            Boolean::make('Featured')->sortable(),

            DateTime::make('Scheduled For'),

            Boolean::make('Published', function () {
                return $this->published;
            })->exceptOnForms(),

            BelongsToMany::make('Tags', 'tags', Tag::class)
                ->searchable(true),
        ];
    }

    /**
     * Get the cards available for the request.
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [
            (new NewPosts)->width('1/2'),
            (new PostsTrend)->width('1/2'),
        ];
    }

    /**
     * Get the filters available for the resource.
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
