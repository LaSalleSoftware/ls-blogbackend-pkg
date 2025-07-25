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

class CreatePostupdatesTable extends BaseMigration
{
    /**
     * The name of the database table
     *
     * @var string
     */
    protected $tableName = "postupdates";

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

                
                if ($this->getColumnType('installed_domains', 'id') == "int") {
                    $table->integer('installed_domain_id')->unsigned()->comment('The domain.');
                } 
                if ($this->getColumnType('installed_domains', 'id') == "biginit") {
                    $table->bigInteger('installed_domain_id')->unsigned()->comment('The domain.');
                }
                $table->foreign('installed_domain_id')->references('id')->on('installed_domains')->onDelete('cascade');

                if ($this->getColumnType('personbydomains', 'id') == "int") {
                    $table->integer('personbydomain_id')->unsigned()->comment('The author.');
                } 
                if ($this->getColumnType('personbydomains', 'id') == "biginit") {
                    $$table->bigInteger('personbydomain_id')->unsigned()->comment('The author.');
                }
                $table->foreign('personbydomain_id')->references('id')->on('personbydomains')->onDelete('cascade');
                
                if ($this->getColumnType('posts', 'id') == "int") {
                    $table->integer('post_id')->unsigned();
                } 
                if ($this->getColumnType('posts', 'id') == "biginit") {
                    $table->bigInteger('post_id')->unsigned();
                }
                $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');

                $table->string('title');
                $table->text('content');
                $table->text('excerpt');

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
