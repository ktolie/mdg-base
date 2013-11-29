<?php
/**
* MDG Type Base Page
*
* @author Matchbox Design Group <info@matchboxdesigngroup.com>
*/
class MDG_Type_Page extends MDG_Type_Base
{
	/**
	 * Class constructor, handles instantiation functionality for the class
	 */
	function __construct()
	{
		$this->post_type        = 'page';
		$this->post_type_title  = 'Pages';
		$this->post_type_single = 'Page';

		$this->_set_mdg_meta_helper_options();

		parent::__construct();
	} // __construct()



	/**
	 * Handles setting of the optional properties of MDG_Meta_Helper
	 *
	 * return Void
	 */
	private function _set_mdg_meta_helper_options() {
		/** @var array Meta box id(s) to be removed */
		// $this->meta_boxes_to_remove = array(
		// 	array(
		// 		'id'      => 'authordiv',
		// 		'context' => 'normal',
		// 		'page'    => $this->post_type,
		// 	),
		// 	array(
		// 		'id'      => 'categorydiv',
		// 		'context' => 'side',
		// 		'page'    => $this->post_type,
		// 	),
		// 	array(
		// 		'id'      => "{$this->post_type}-categorydiv",
		// 		'context' => 'side',
		// 		'page'    => $this->post_type,
		// 	),
		// 	array(
		// 		'id'      => 'commentstatusdiv',
		// 		'context' => 'normal',
		// 		'page'    => $this->post_type,
		// 	),
		// 	array(
		// 		'id'      => 'commentsdiv',
		// 		'context' => 'normal',
		// 		'page'    => $this->post_type,
		// 	),
		// 	array(
		// 		'id'      => 'formatdiv',
		// 		'context' => 'normal',
		// 		'page'    => $this->post_type,
		// 	),
		// 	array(
		// 		'id'      => 'pageparentdiv',
		// 		'context' => 'side',
		// 		'page'    => $this->post_type,
		// 	),
		// 	array(
		// 		'id'      => 'postexcerpt',
		// 		'context' => 'normal',
		// 		'page'    => $this->post_type,
		// 	),
		// 	array(
		// 		'id'      => 'revisionsdiv',
		// 		'context' => 'normal',
		// 		'page'    => $this->post_type,
		// 	),
		// 	array(
		// 		'id'      => 'slugdiv',
		// 		'context' => 'normal',
		// 		'page'    => $this->post_type,
		// 	),
		// 	array(
		// 		'id'      => 'submitdiv',
		// 		'context' => 'side',
		// 		'page'    => $this->post_type,
		// 	),
		// 	array(
		// 		'id'      => 'tagsdiv-post_tag',
		// 		'context' => 'side',
		// 		'page'    => $this->post_type,
		// 	),
		// 	array(
		// 		'id'      => 'trackbacksdiv',
		// 		'context' => 'normal',
		// 		'page'    => $this->post_type,
		// 	),
		// );
	} // _set_mdg_meta_helper_options()



	/**
	 * Creates custom meta fields array.
	 *
	 * @return ArrayObject Custom meta fields
	 */
	public function get_custom_meta_fields() {
		return array();
	} // get_custom_meta_fields()



	/**
	 * Disables creating post type since page is a default post type
	 *
	 * @return Void
	 */
	public function register_post_type() {}
} // END Class MDG_Type_Page()

global $mdg_page;
$mdg_page = new MDG_Type_Page();
