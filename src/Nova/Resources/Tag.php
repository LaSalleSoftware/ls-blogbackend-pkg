<?php

/**
 * This file is part of the Lasalle Software blog back-end package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright  (c) 2019-2020 The South LaSalle Trading Corporation
 * @license    http://opensource.org/licenses/MIT
 * @author     Bob Bloom
 * @email      bob.bloom@lasallesoftware.ca
 * @link       https://lasallesoftware.ca
 * @link       https://packagist.org/packages/lasallesoftware/ls-blogbackend-pkg
 * @link       https://github.com/LaSalleSoftware/ls-blogbackend-pkg
 *
 */

namespace Lasallesoftware\Blogbackend\Nova\Resources;

// LaSalle Software classes
use Lasallesoftware\Librarybackend\Authentication\Models\Personbydomain;
use Lasallesoftware\Novabackend\Nova\Fields\LookupDescription;
use Lasallesoftware\Novabackend\Nova\Fields\LookupEnabled;
use Lasallesoftware\Novabackend\Nova\Fields\Title;
use Lasallesoftware\Novabackend\Nova\Fields\Uuid;
use Lasallesoftware\Novabackend\Nova\Resources\BaseResource;

// Laravel Nova classes
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;

// Laravel class
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Laravel facade
use Illuminate\Support\Facades\Auth;


/**
 * Class Tag
 *
 * @package Lasallesoftware\Blogbackend\Nova\Resources
 */
class Tag extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'Lasallesoftware\\Blogbackend\\Models\\Tag';

    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Blog';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'title';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'title',
    ];


    /**
     * Determine if this resource is available for navigation.
     *
     * Only the owner role can see this resource in navigation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public static function availableForNavigation(Request $request)
    {
        return Personbydomain::find(Auth::id())->IsOwner()
             || Personbydomain::find(Auth::id())->IsSuperadministrator()
             // || Personbydomain::find(Auth::id())->IsAdministrator()
        ;
    }

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return __('lasallesoftwareblogbackend::blogbackend.resource_label_plural_tags');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        return __('lasallesoftwareblogbackend::blogbackend.resource_label_singular_tags');
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('Installed_domain', 'installed_domain', 'Lasallesoftware\Novabackend\Nova\Resources\Installed_domain')
                ->creationRules('required')
                ->updateRules('required')
                ->hideFromIndex()
            ,

            Title::make(__('lasallesoftwarelibrarybackend::general.field_name_title'))
                ->creationRules('unique:tags,title')
                ->updateRules('unique:tags,title,{{resourceId}}')
            ,

            LookupDescription::make('description')
                ->hideFromDetail()
            ,

            LookupEnabled::make('enabled'),

            Uuid::make('uuid'),

            BelongsToMany::make('Post')->singularLabel('Post'),

            new Panel(__('lasallesoftwarelibrarybackend::general.panel_system_fields'), $this->systemFields()),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }

    /**
     * Build a "relatable" query for the given resource.
     *
     * This query determines which instances of the model may be attached to other resources.
     *
     * This method is in the Laravel\Nova\PerformsQueries trait.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder    $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function relatableQuery(NovaRequest $request, $query)
    {
        // (1) Exclude the tags already associated (attached) to a post in the "Attach Tag (to post)" drop-down
        // because we do not want tags that are already associated (attached) to the post to appear in the
        // drop-down (can you tell that it is well past midnight?!).

        // $request->resourceId is the id of the posts db table, not the tags db table
        $tagsAttachedToPost = DB::table('post_tag')->where('post_id', $request->resourceId)->get();

        // (2) tags must belong to the post's installed_domain_id
        $post_installed_domain_id = DB::table('posts')->where('id', $request->resourceId)->pluck('installed_domain_id')->first();

        // the second param of "whereNotIn" must be an array
        return $query->whereNotIn('id', $tagsAttachedToPost->pluck('tag_id')->toArray())
                     ->where('installed_domain_id', $post_installed_domain_id)
        ;
    }

    /**
     * Build an "index" query for the given resource.
     *
     * Overrides Laravel\Nova\Actions\ActionResource::indexQuery
     *
     * Since Laravel's policies do *NOT* include an action for the controller's INDEX action,
     * we have to override Nova's resource indexQuery method.
     *
     * So, we are going to mimick here what the "index" policy would do.
     *
     *   * Limit the index view where the user's installed_domain_id = the model's installed_domain_id.
     *   * Owners see all the categories from all the domains
     *   * Super Admins see categories associated with their domains only
     *   * Admins do not see any categories
     *
     *
     * Called from a resource's indexQuery() method.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder    $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        // owners see all categories
        if (auth()->user()->hasRole('owner')) {
            return $query;
        }

        // super admins see categories belonging to their own domain
        if (auth()->user()->hasRole('superadministrator')) {
            return $query->where('installed_domain_id', '=', auth()->user()->installed_domain_id);
        }

        // admins are not allowed to mess with categories
        // they are not supposed to see the "Categories" menu item, but maybe they end up at the index endpoint anyways
        return $query->where('installed_domain_id', '=',0);
    }
}
