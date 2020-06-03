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
use Lasallesoftware\Novabackend\Nova\Fields\Excerpt;
use Lasallesoftware\Novabackend\Nova\Fields\LookupEnabled;
use Lasallesoftware\Novabackend\Nova\Fields\Title;
use Lasallesoftware\Novabackend\Nova\Fields\Uuid;
use Lasallesoftware\Novabackend\Nova\Resources\BaseResource;

// Laravel Nova classes
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;

// Laravel class
use Illuminate\Http\Request;

// Laravel facade
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


/**
 * Class Postupdate
 *
 * @package Lasallesoftware\Blogbackend\Nova\Resources
 */
class Postupdate extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'Lasallesoftware\\Blogbackend\\Models\\Postupdate';

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
        // owner can create post updates for all posts
        if ((Personbydomain::find(Auth::id())->IsOwner()) && (self::isPostsForOwner())) return true;

        // super admin can create post updates for posts associated with their domain
        if ((Personbydomain::find(Auth::id())->IsSuperadministrator()) && (self::isPostsForSuperadministrator())) return true;

        // admin can create post updates for posts that they authored
        if ((Personbydomain::find(Auth::id())->IsAdministrator()) && (self::isPostsForAdministrator())) return true;

        // still here may mean that there are no posts available
        return false;
    }

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return __('lasallesoftwareblogbackend::blogbackend.resource_label_plural_postupdates');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        return __('lasallesoftwareblogbackend::blogbackend.resource_label_singular_postupdates');
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

            BelongsTo::make('Post', 'post', 'Lasallesoftware\Blogbackend\Nova\Resources\Post')
                ->help('<ul>
                         <li>'. __('lasallesoftwarelibrarybackend::general.field_help_required') .'</li>
                     </ul>')
                ->creationRules('required')
                ->updateRules('required')
                ->sortable()
                ->searchable()
                ->hideWhenUpdating()
            ,

            Title::make(__('lasallesoftwarelibrarybackend::general.field_name_title'))
                ->creationRules('unique:postupdates,title')
                ->updateRules('unique:postupdates,title,{{resourceId}}')
            ,

            Trix::make(__('lasallesoftwarelibrarybackend::general.field_name_content'))
                ->alwaysShow()
                ->help('<ul>
                         <li>'. __('lasallesoftwarelibrarybackend::general.field_help_required') .'</li>
                     </ul>')
                ->creationRules('required')
                ->updateRules('required')
                ->hideFromIndex()
            ,

            Excerpt::make(__('lasallesoftwarelibrarybackend::general.field_name_excerpt'))
                ->hideFromIndex()
            ,

            Date::make(__('lasallesoftwarelibrarybackend::general.field_name_publish_on'))
                ->format('DD MMM YYYY')
                ->help('<ul>
                         <li>'. __('lasallesoftwarelibrarybackend::general.field_help_publish_on1') .'</li>
                         <li>'. __('lasallesoftwarelibrarybackend::general.field_help_publish_on2') .'</li>
                     </ul>')
                ->sortable()
               //->creationRules('date', 'after_or_equal:today')
               //->updateRules('date', 'after_or_equal:today') (do not want to modify the date that was originally entered)
            ,

            LookupEnabled::make('enabled'),

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
        self::getRelatableQueryForThisResource($query);
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
        // owners see all post updates
        if (auth()->user()->hasRole('owner')) {
            return $query;
        }

        // super admins see postupdates belonging to their own domain
        if (auth()->user()->hasRole('superadministrator')) {
            return $query->where('installed_domain_id', '=', auth()->user()->installed_domain_id);
        }

        // admins see postupdates that they authored
        if (auth()->user()->hasRole('administrator')) {
            return $query->where('personbydomain_id', '=', auth()->user()->id);
        }

        // still here? not supposed to see the "Post Updates" menu item, but maybe they end up at the index endpoint anyways
        return $query->where('installed_domain_id', '=',0);
    }



    // I ADAPTED THE FOLLOWING THREE METHODS FROM Lasallesoftware\Blogbackend\Policies\PostupdatePolicy

    /**
     * Are there posts available to an owner?
     *
     * @return bool
     */
    private static function isPostsForOwner()
    {
        return (DB::table('posts')->count() == 0) ? false : true;
    }

    /**
     * Are there posts available to a super administrator?
     *
     * @return bool
     */
    private static function isPostsForSuperadministrator()
    {
        return (DB::table('posts')->where('installed_domain_id', Personbydomain::find(Auth::id())->installed_domain_id)->count() == 0) ? false : true;
    }

    /**
     * Are there posts available to an administrator?
     *
     * @return bool
     */
    private static function isPostsForAdministrator()
    {
        return (DB::table('posts')->where('personbydomain_id', Auth::id())->count() == 0) ? false : true;
    }
}
