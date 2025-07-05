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
 * @copyright  (c) 2019-2025 The South LaSalle Trading Corporation
 * @license    http://opensource.org/licenses/MIT
 * @author     Bob Bloom
 * @email      bob.bloom@lasallesoftware.ca
 * @link       https://lasallesoftware.ca
 * @link       https://packagist.org/packages/lasallesoftware/ls-blogbackend-pkg
 * @link       https://github.com/LaSalleSoftware/ls-blogbackend-pkg
 *
 */

// LaSalle Software
use Lasallesoftware\Librarybackend\Database\Migrations\BaseMigration;

// Laravel classes
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CreatePostsTable extends BaseMigration
{
    /**
     * The name of the database table
     *
     * @var string
     */
    protected $tableName = "posts";

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if ((!Schema::hasTable($this->tableName)) &&
            ($this->doTheMigration(env('APP_ENV'), env('LASALLE_APP_NAME')))) {

            Schema::create($this->tableName, function (Blueprint $table) {
                $table->engine = 'InnoDB';

                $table->increments('id');

                $this->createForeignIdFieldAndReference('installed_domains', 'id', 'installed_domain_id', $table, false);

                $this->createForeignIdFieldAndReference('personbydomains', 'id', 'personbydomain_id', $table, false);

                $this->createForeignIdFieldAndReference('categories', 'id', 'category_id', $table, false, true);


                $table->string('title');
                $table->string('slug')->unique();
                $table->text('content');
                $table->text('excerpt');
                $table->string('meta_description');
                $table->text('featured_image_upload')->nullable();
                $table->text('featured_image_code')->nullable();
                $table->text('featured_image_external_file')->nullable();
                $table->boolean('enabled')->default(true);
                $table->date('publish_on')->useCurrent();

                $table->uuid('uuid')->nullable();

                $table->timestamp('created_at')->useCurrent();
                $table->integer('created_by')->unsigned()->default(1);
                //$table->foreign('created_by')->references('id')->on('persons');

                $table->timestamp('updated_at')->nullable();
                $table->integer('updated_by')->unsigned()->nullable();
                //$table->foreign('updated_by')->references('id')->on('persons');

                $table->timestamp('locked_at')->nullable();
                $table->integer('locked_by')->unsigned()->nullable();
                //$table->foreign('locked_by')->references('id')->on('persons');
            });
        }
    }
}