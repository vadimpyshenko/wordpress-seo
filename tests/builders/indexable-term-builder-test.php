<?php

namespace Yoast\WP\Free\Tests\Builders;

use Brain\Monkey;
use Mockery;
use Yoast\WP\Free\Builders\Indexable_Term_Builder;
use Yoast\WP\Free\Helpers\Image_Helper;
use Yoast\WP\Free\Models\Indexable;
use Yoast\WP\Free\ORM\ORMWrapper;
use Yoast\WP\Free\Tests\TestCase;

/**
 * Class Indexable_Term_Test.
 *
 * @group indexables
 * @group builders
 *
 * @coversDefaultClass \Yoast\WP\Free\Builders\Indexable_Term_Builder
 * @covers ::<!public>
 *
 * @package Yoast\Tests\Builders
 */
class Indexable_Term_Builder_Test extends TestCase {

	/**
	 * Options being mocked.
	 *
	 * @var array
	 */
	protected $mocked_options = [ 'wpseo', 'wpseo_titles', 'wpseo_social', 'wpseo_ms' ];

	/**
	 * Tests the formatting of the indexable data.
	 *
	 * @covers ::build
	 */
	public function test_build() {
		Monkey\Functions\expect( 'get_term' )->once()->with( 1 )->andReturn( (object) [ 'taxonomy' => 'category', 'term_id' => 1 ] );
		Monkey\Functions\expect( 'get_term_link' )->once()->with( 1, 'category' )->andReturn( 'https://example.org/category/1' );
		Monkey\Functions\expect( 'is_wp_error' )->twice()->andReturn( false );
		Monkey\Functions\expect( 'get_option' )->once()->with( 'wpseo_taxonomy_meta' )->andReturn(
			[
				'category' => [
					1 => [
						'wpseo_focuskw'               => 'focuskeyword',
						'wpseo_linkdex'               => '75',
						'wpseo_noindex'               => 'noindex',
						'wpseo_meta-robots-adv'       => '',
						'wpseo_content_score'         => '50',
						'wpseo_canonical'             => 'https://canonical-term',
						'wpseo_meta-robots-nofollow'  => '1',
						'wpseo_title'                 => 'title',
						'wpseo_desc'                  => 'description',
						'wpseo_bctitle'               => 'breadcrumb_title',
						'wpseo_opengraph-title'       => 'og_title',
						'wpseo_opengraph-image'       => 'og_image',
						'wpseo_opengraph-image-id'    => 'og_image_id',
						'wpseo_opengraph-description' => 'og_description',
						'wpseo_twitter-title'         => 'twitter_title',
						'wpseo_twitter-image'         => 'twitter_image',
						'wpseo_twitter-description'   => 'twitter_description',
					],
				],
			]
		);

		$indexable_mock      = Mockery::mock( Indexable::class );
		$indexable_mock->orm = Mockery::mock( ORMWrapper::class );
		$indexable_mock->orm->expects( 'set' )->with( 'object_id', 1 );
		$indexable_mock->orm->expects( 'set' )->with( 'object_type', 'term' );
		$indexable_mock->orm->expects( 'set' )->with( 'object_sub_type', 'category' );
		$indexable_mock->orm->expects( 'set' )->with( 'permalink', 'https://example.org/category/1' );
		$indexable_mock->orm->expects( 'set' )->with( 'canonical', 'https://canonical-term' );
		$indexable_mock->orm->expects( 'set' )->with( 'title', 'title' );
		$indexable_mock->orm->expects( 'set' )->with( 'breadcrumb_title', 'breadcrumb_title' );
		$indexable_mock->orm->expects( 'set' )->with( 'description', 'description' );
		$indexable_mock->orm->expects( 'set' )->with( 'og_title', 'og_title' );
		$indexable_mock->orm->expects( 'set' )->with( 'og_image', 'og_image' );
		$indexable_mock->orm->expects( 'set' )->with( 'og_image', null );
		$indexable_mock->orm->expects( 'set' )->with( 'og_image', 'image.jpg' );
		$indexable_mock->orm->expects( 'set' )->with( 'og_image_id', 'og_image_id' );
		$indexable_mock->orm->expects( 'set' )->with( 'og_image_id', null );
		$indexable_mock->orm->expects( 'set' )->with( 'og_image_source', null );
		$indexable_mock->orm->expects( 'set' )->with( 'og_image_source', 'first-content-image' );
		$indexable_mock->orm->expects( 'set' )->with( 'og_image_meta', null );
		$indexable_mock->orm->expects( 'set' )->with( 'og_description', 'og_description' );
		$indexable_mock->orm->expects( 'set' )->with( 'twitter_title', 'twitter_title' );
		$indexable_mock->orm->expects( 'set' )->with( 'twitter_image', 'twitter_image' );
		$indexable_mock->orm->expects( 'set' )->with( 'twitter_image', null );
		$indexable_mock->orm->expects( 'set' )->with( 'twitter_image', 'image.jpg' );
		$indexable_mock->orm->expects( 'set' )->with( 'twitter_image_id', null );
		$indexable_mock->orm->expects( 'set' )->with( 'twitter_image_source', null );
		$indexable_mock->orm->expects( 'set' )->with( 'twitter_image_source', 'first-content-image' );
		$indexable_mock->orm->expects( 'set' )->with( 'twitter_description', 'twitter_description' );
		$indexable_mock->orm->expects( 'set' )->with( 'is_cornerstone', false );
		$indexable_mock->orm->expects( 'set' )->with( 'is_robots_noindex', true );
		$indexable_mock->orm->expects( 'set' )->with( 'is_robots_nofollow', null );
		$indexable_mock->orm->expects( 'set' )->with( 'is_robots_noarchive', null );
		$indexable_mock->orm->expects( 'set' )->with( 'is_robots_noimageindex', null );
		$indexable_mock->orm->expects( 'set' )->with( 'is_robots_nosnippet', null );
		$indexable_mock->orm->expects( 'set' )->with( 'primary_focus_keyword', 'focuskeyword' );
		$indexable_mock->orm->expects( 'set' )->with( 'primary_focus_keyword_score', 75 );
		$indexable_mock->orm->expects( 'set' )->with( 'readability_score', 50 );

		$indexable_mock->orm->expects( 'offsetExists' )->once()->with( 'canonical' )->andReturnTrue();
		$indexable_mock->orm->expects( 'get' )->once()->with( 'canonical' )->andReturn( 'https://canonical-term' );

		$indexable_mock->orm->expects( 'get' )->once()->with( 'og_image' );
		$indexable_mock->orm->expects( 'get' )->times( 2 )->with( 'og_image_id' );
		$indexable_mock->orm->expects( 'get' )->twice()->with( 'og_image_source' );
		$indexable_mock->orm->expects( 'get' )->twice()->with( 'twitter_image' );
		$indexable_mock->orm->expects( 'get' )->times( 3 )->with( 'twitter_image_id' );
		$indexable_mock->orm->expects( 'get' )->with( 'object_id' );

		$image_helper     = Mockery::mock( Image_Helper::class );
		$open_graph_image = Mockery::mock( \Yoast\WP\Free\Helpers\Open_Graph\Image_Helper::class );
		$twitter_image    = Mockery::mock( \Yoast\WP\Free\Helpers\Twitter\Image_Helper::class );

		$image_helper
			->expects( 'get_term_content_image' )
			->once()
			->andReturn( 'image.jpg' );

		$builder = new Indexable_Term_Builder();

		$builder->set_social_image_helpers(
			$image_helper,
			$open_graph_image,
			$twitter_image
		);

		$builder->build( 1, $indexable_mock );
	}
}
