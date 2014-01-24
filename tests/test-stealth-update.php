<?php

class Stealth_Update_Test extends WP_UnitTestCase {

	function tearDown() {
		parent::tearDown();
	}



	/**
	 * HELPER FUNCTIONS
	 */



	private function stealthify( $post_id ) {
		add_post_meta( $post_id, '_stealth-update', '1' );
	}

	private function get_id( $object ) {
		return $object->ID;
	}



	/**
	 * FUNCTIONS FOR HOOKING ACTIONS/FILTERS
	 */



	/**
	 * TESTS
	 */



	function test_non_stealth_post_not_affected_on_update() {
		$date = '2014-01-03 12:01:30';
		$post_id = $this->factory->post->create( array( 'post_modified' => $date ) );

		$post = get_post( $post_id, ARRAY_A );

		$post['post_title'] = 'New title';
		wp_update_post( $post );

		$post = get_post( $post_id );

		$this->assertNotEquals( $date, $post->post_modified );
	}

	function test_stealth_post_modified_date_unchanged_on_update() {
		$date = '2014-01-03 12:01:30';
		$post_id = $this->factory->post->create( array( 'post_modified' => $date ) );

		$post = get_post( $post_id, ARRAY_A );

		$post['post_title']             = 'New title';
		$post['stealth_update']         = '1';
		$post['previous_last_modified'] = $date;

		wp_update_post( $post );

		$post = get_post( $post_id );

		$this->assertEquals( 'New title', $post->post_title );
		$this->assertEquals( $date, $post->post_modified );
		$this->assertEquals( get_gmt_from_date( $date ), $post->post_modified_gmt );
	}

	function test_stealth_post_saves_meta_on_update() {
		$date = '2014-01-03 12:01:30';
		$post_id = $this->factory->post->create( array( 'post_modified' => $date ) );

		$post = get_post( $post_id, ARRAY_A );

		$post['post_title']             = 'New title';
		$post['stealth_update']         = '1';
		$post['previous_last_modified'] = $date;

		wp_update_post( $post );

		$post = get_post( $post_id );

		$this->assertEquals( '1', get_post_meta( $post_id, '_stealth-update', true ) );
	}

	function test_revision_of_stealth_post_not_affected_on_update() {
		$date = '2014-01-03 12:01:30';
		$post_id = $this->factory->post->create( array( 'post_modified' => $date, 'post_type' => 'revision' ) );

		$post = get_post( $post_id, ARRAY_A );

		$post['post_title']             = 'New title';
		$post['stealth_update']         = '1';
		$post['previous_last_modified'] = $date;

		wp_update_post( $post );

		$post = get_post( $post_id );

		$this->assertNotEquals( $date, $post->post_modified );
	}

}
