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
 * @copyright  (c) 2019-2024 The South LaSalle Trading Corporation
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
use Lasallesoftware\Novabackend\Nova\Fields\Slug;
use Lasallesoftware\Novabackend\Nova\Fields\Title;
use Lasallesoftware\Novabackend\Nova\Fields\Uuid;
use Lasallesoftware\Novabackend\Nova\Resources\BaseResource;

// Laravel Nova classes
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;

// Laravel class
use Illuminate\Http\Request;

// Laravel facade
use Illuminate\Support\Facades\Auth;


/**
 * Class Post
 *
 * @package Lasallesoftware\Blogbackend\Nova\Resources
 */
class Post extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'Lasallesoftware\\Blogbackend\\Models\\Post';

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
            || Personbydomain::find(Auth::id())->IsAdministrator();
    }

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return __('lasallesoftwareblogbackend::blogbackend.resource_label_plural_posts');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        return __('lasallesoftwareblogbackend::blogbackend.resource_label_singular_posts');
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('Installed_domain', 'installed_domain', 'Lasallesoftware\Novabackend\Nova\Resources\Installed_domain')
                ->creationRules('required')
                ->updateRules('required')
                ->sortable(),

            Title::make(__('lasallesoftwarelibrarybackend::general.field_name_title'))
                ->creationRules('unique:posts,title')
                ->updateRules('unique:posts,title,{{resourceId}}'),

            Slug::make('slug')
                ->hideFromIndex(),

            Trix::make(__('lasallesoftwarelibrarybackend::general.field_name_content'))
                ->alwaysShow()
                ->help('<ul>
                         <li>' . __('lasallesoftwarelibrarybackend::general.field_help_required') . '</li>
                     </ul>')
                ->creationRules('required')
                ->updateRules('required')
                ->hideFromIndex(),

            Excerpt::make(__('lasallesoftwarelibrarybackend::general.field_name_excerpt'))
                ->hideFromIndex(),

            Text::make(__('lasallesoftwarelibrarybackend::general.field_name_meta_description'))
                ->help('<ul>
                         <li>' . __('lasallesoftwarelibrarybackend::general.field_help_meta_description1') . '</li>
                         <li>' . __('lasallesoftwarelibrarybackend::general.field_help_meta_description2') . '</li>
                     </ul>')
                ->hideFromIndex()
                ->creationRules('max:255')
                ->updateRules('max:255'),

            LookupEnabled::make(__('lasallesoftwarelibrarybackend::general.field_name_lookup_enabled')),

            Boolean::make(__('lasallesoftwarelibrarybackend::general.field_name_preview_in_frontend'), 'preview_in_frontend')
                ->help('<ul>
                            <li>' . __('lasallesoftwarelibrarybackend::general.field_help_preview_in_frontend1') . '</li>
                            <li>' . __('lasallesoftwarelibrarybackend::general.field_help_preview_in_frontend2') . '</li>
                        </ul>')
            ,

            Date::make(__('lasallesoftwarelibrarybackend::general.field_name_publish_on'))
                // ->format('DD MMM YYYY')
                ->help('<ul>
                         <li>' . __('lasallesoftwarelibrarybackend::general.field_help_publish_on1') . '</li>
                         <li>' . __('lasallesoftwarelibrarybackend::general.field_help_publish_on2') . '</li>
                     </ul>')
                ->sortable()
            //->creationRules('date', 'after_or_equal:today')
            //->updateRules('date', 'after_or_equal:today') (do not want to modify the date that was originally entered)
            ,

            BelongsTo::make('Category')
                ->help('<ul>
                         <li>' . __('lasallesoftwareblogbackend::blogbackend.field_help_contact_only_see_when_editing') . '</li>
                         <li>' . __('lasallesoftwarelibrarybackend::general.field_help_optional') . '</li>
                     </ul>')
                ->nullable()
                ->sortable(),

            Uuid::make('uuid'),    

            new Panel(__('lasallesoftwarelibrarybackend::general.panel_featured_image_fields'), $this->featuredimageFields()),

            BelongsToMany::make('Tag')->singularLabel('Tag'),

            HasMany::make('Postupdate'),

            //HasOne::make('Lookup_domain', 'lookup_domain', 'Lasallesoftware\Librarybackend\Nova\Resources\Lookup_domain'),
            //HasOne::make('Personbydomain'),

            new Panel(__('lasallesoftwarelibrarybackend::general.panel_system_fields'), $this->systemFields()),
        ];
    }

    /**
     * The featured image fields.
     *
     * @return array
     */
    public function featuredimageFields()
    {
        return [
            Image::make(__('lasallesoftwarelibrarybackend::general.field_name_featured_image_upload'))
                ->disk(config('lasallesoftware-librarybackend.lasalle_filesystem_disk_where_images_are_stored'))
                ->disableDownload()
                ->help(
                    '<ul>
                         <li>' . __('lasallesoftwarelibrarybackend::general.field_help_optional') . '</li>
                     </ul>'
                )
                ->hideFromIndex()
                ->squared('true')
                ->path(config('lasallesoftware-librarybackend.image_path_for_post_nova_resource'))
                ->maxWidth(100),

            Textarea::make(__('lasallesoftwarelibrarybackend::general.field_name_featured_image_code'))
                ->alwaysShow()
                ->help(
                    '<ul>
                         <li>' . __('lasallesoftwarelibrarybackend::general.field_help_optional') . '</li>
                         <li>' . __('lasallesoftwarelibrarybackend::general.field_help_featured_image_code1') . '</li>
                         <li>' . __('lasallesoftwarelibrarybackend::general.field_help_featured_image_code2') . '</li>
                     </ul>'
                )
                ->hideFromIndex(),

            Text::make(__('lasallesoftwarelibrarybackend::general.field_name_featured_image_external_file'))
                ->help(
                    '<ul>
                         <li>' . __('lasallesoftwarelibrarybackend::general.field_help_optional') . '</li>
                         <li>' . __('lasallesoftwarelibrarybackend::general.field_help_featured_image_external1') . '</li>
                         <li>' . __('lasallesoftwarelibrarybackend::general.field_help_featured_image_external2') . '</li>
                         <li>' . __('lasallesoftwarelibrarybackend::general.field_help_featured_image_external3') . '</li>
                         <li>' . __('lasallesoftwarelibrarybackend::general.field_help_featured_image_external4') . '</li>
                     </ul>'
                )
                ->hideFromIndex(),
        ];
    }


    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(NovaRequest $request)
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
     * https://nova.laravel.com/docs/1.0/resources/authorization.html#relatable-filtering
     *
     *
     *   ==> SEE NOTE IN indexQuery() method below!! <==
     *
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder    $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function relatableQuery(NovaRequest $request, $query)
    {
        // owners see all posts in the dropdown
        if (Personbydomain::find(Auth::id())->IsOwner()) {
            return $query
                //->where('enabled', 1)
            ;
        }

        // super admins see posts for their domain in the dropdown
        if (Personbydomain::find(Auth::id())->IsSuperadministrator()) {
            return $query
                ->where('installed_domain_id', Personbydomain::find(Auth::id())->installed_domain_id)
                //->where('enabled', 1)
            ;
        }

        // admins see posts that they authored in the dropdown
        if (Personbydomain::find(Auth::id())->IsSuperadministrator()) {
            return $query
                ->where('personbydomain_id', Personbydomain::find(Auth::id())->id)
                //->where('enabled', 1)
            ;
        }

        // otherwise, display only the installed domain that that user belongs
        return $query
            ->where('personbydomain_id', Personbydomain::find(Auth::id())->id)
            //->where('enabled', 1)
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
        // owners see all posts
        if (auth()->user()->hasRole('owner')) {
            return $query;
        }

        // super admins see posts belonging to their own domain
        if (auth()->user()->hasRole('superadministrator')) {
            return $query->where('installed_domain_id', auth()->user()->installed_domain_id);
        }

        // admins see posts that they authored
        if (auth()->user()->hasRole('administrator')) {
            return $query->where('personbydomain_id', auth()->user()->id);
        }

        // still here? not supposed to see the "Posts" menu item, but maybe they end up at the index endpoint anyways
        return $query->where('installed_domain_id', 0);
    }
}
