<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
include_once SFTCY_WORDFORM_PLUGIN_DIR . 'admin/views/pages-header-template.php';
?>
<div class="wrap" id="wordformAllFormsWrapperTable" style="display: none;">
	<h3><i class="dashicons dashicons-editor-table dashicon-custom-color"></i>&nbsp;<?php esc_html_e( 'All Created Forms', 'wordform' ); ?></h3>
	<table id="wordformAllFormsTable" class="display hover stripe">
		<thead>
			<tr>
				<th><?php esc_html_e( 'FormName', 'wordform' ); ?></th>				
				<th><?php esc_html_e( 'Shortcode', 'wordform' ); ?></th>
				<th><?php esc_html_e( 'Attach', 'wordform' ); ?></th>
				<th><?php esc_html_e( 'Created', 'wordform' ); ?></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php
			if ( isset( $query_results ) && array_filter( $query_results ) ) {
				foreach ( $query_results as $data ) {
					$form_id   = sanitize_text_field( $data['form_id'] );
					$edit_link = admin_url( 'admin.php?page=sc-wordform-create-forms&wordform-edit-id=' . $form_id );

					$attach_with_new_post_link          = admin_url( 'post-new.php' );
					$attach_wordform_with_new_post_link = add_query_arg( array( 'attach-wordform-id' => $form_id ), $attach_with_new_post_link );
					$attach_with_new_page_link          = admin_url( 'post-new.php?post_type=page' );
					$attach_wordform_with_new_page_link = add_query_arg( array( 'attach-wordform-id' => $form_id ), $attach_with_new_page_link );
					?>
			<tr>
				<td><?php echo esc_html( $data['form_name'] ); ?></td>				
				<td class="sc-wordform-shortcode-display-td">
					<?php
					$shortcode         = '[wordform wid="' . $form_id . '"]';
					$copt_clipboard_id = 'sc-wordform-copy-shortcode-' . $form_id;
					?>
					<span id="<?php echo esc_attr( $copt_clipboard_id ); ?>">
						<?php echo esc_html( $shortcode ); ?>
					</span>					
				</td>
				<td class="sc-wordform-attachto-display-td">
					<span>
						<i class="fa-solid fa-paperclip"></i> 
						<?php esc_html_e( 'Attach to ', 'wordform' ); ?>
						<a href="<?php echo esc_url( $attach_wordform_with_new_post_link ); ?>" title="<?php esc_html_e( 'Attach with new Post', 'wordform' ); ?>"><?php esc_html_e( 'Post', 'wordform' ); ?></a> | 
						<a href="<?php echo esc_url( $attach_wordform_with_new_page_link ); ?>" title="<?php esc_html_e( 'Attach with new Page', 'wordform' ); ?>" ><?php esc_html_e( 'Page', 'wordform' ); ?></a> 
					</span>
					 
					<span class="sc-wordform-shortcode-copy-text" style="display: none;" data-clipboard-text='<?php echo esc_attr( $shortcode ); ?>' > | 
					<i class="fa-regular fa-copy"></i> <?php esc_html_e( 'Copy Shortcode', 'wordform' ); ?></span>										 					
				</td>
				<td>
					<?php
					$date_created = date_format( date_create( $data['created_at'] ), 'j-M-y' );
					echo esc_html( $date_created );
					?>
				</td>
				<td class="sc-wordform-action-btns-display-td">
					<a href="<?php echo esc_url( $edit_link ); ?>" title="<?php esc_html_e( 'Edit', 'wordform' ); ?>">
						<i class="fa-solid fa-pencil"></i> <?php esc_html_e( 'Edit', 'wordform' ); ?>
					</a>
					&nbsp;|&nbsp;
					<a href="<?php echo esc_url( site_url() . '?sc-wordform-id=' . $form_id ); ?>" target="_blank" title="<?php esc_html_e( 'Preview & Test', 'wordform' ); ?>">
						<i class="fa-regular fa-eye"></i> <?php esc_html_e( 'View', 'wordform' ); ?>
					</a>
					&nbsp;|&nbsp;
					<a data-sc-wordform-name="<?php echo esc_attr( $data['form_name'] ); ?>" data-sc-wordform-id="<?php echo esc_attr( $form_id ); ?>" class="sc-wordform-delete-btn" href="javascript:void(0)" title="<?php esc_html_e( 'Delete Form', 'wordform' ); ?>">
						<i class="fa-solid fa-trash-can"></i>
					</a>
					
				</td>
			</tr>
					<?php
				}
			}
			?>
		</tbody>
	</table>						
</div><!-- #wordformAllFormsTable -->
