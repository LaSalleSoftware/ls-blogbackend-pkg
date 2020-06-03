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
use Lasallesoftware\Novabackend\Nova\Fields\LookupDomain;
use Lasallesoftware\Novabackend\Nova\Fields\LookupEnabled;
use Lasallesoftware\Novabackend\Nova\Fields\Title;
use Lasallesoftware\Novabackend\Nova\Fields\Uuid;
use Lasallesoftware\Novabackend\Nova\Resources\BaseResource;

// Laravel Nova classes
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;

// Laravel class
use Illuminate\Http\Request;

// Laravel facade
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


/**
 * Class Category
 *
 * @package Lasallesoftware\Blogbackend\Nova\Resources
 */
class Category extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'Lasallesoftware\\Blogbackend\\Models\\Category';

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
        'id', 'title', 'installed_domain_id'
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
        return __('lasallesoftwareblogbackend::blogbackend.resource_label_plural_categories');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        return __('lasallesoftwareblogbackend::blogbackend.resource_label_singular_categories');
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
                ->creationRules('unique:categories,title')
                ->updateRules('unique:categories,title,{{resourceId}}')
            ,

            Trix::make(__('lasallesoftwarelibrarybackend::general.field_name_content'))
                ->alwaysShow()
                ->creationRules('required')
                ->updateRules('required')
                ->hideFromIndex()
            ,

            LookupDescription::make('description')
            //->hideFromIndex()
            ,

            Image::make( __('lasallesoftwarelibrarybackend::general.field_name_featured_image'))
                ->disk(config('lasallesoftware-librarybackend.lasalle_filesystem_disk_where_images_are_stored'))
                ->disableDownload()
                ->help('<ul>
                         <li>'. __('lasallesoftwarelibrarybackend::general.field_help_optional') .'</li>
                     </ul>'
                )
                ->squared('true')
                ->path(config('lasallesoftware-librarybackend.image_path_for_category_nova_resource')),

            LookupEnabled::make('enabled'),

            HasMany::make('Post'),

            Uuid::make('uuid'),

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
        // display categories that belong to the same installed_domain_id as the post's installed_domain_id.
        // Yes, this will result in a no categories displaying in the "create post". I have new help text for this. 
        $post_installed_domain_id = DB::table('posts')->where('id', $request->resourceId)->pluck('installed_domain_id')->first();

        return $query->where('installed_domain_id', $post_installed_domain_id);
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
