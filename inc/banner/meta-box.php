<?php


class BannerMeta {

    public function init() {
        $this->registerMetaBox();
    }

    private function registerMetaBox() {

        function add_banner_meta_box() {
            $screen = 'banner-cpt'; // choose 'post' or 'page' or custom post
            add_meta_box( 
                'banner_meta_box', // $id
                __( 'Banner Settings', 'bsx-wordpress' ), // $title
                'show_banner_meta_box', // $callback
                $screen, // $screen
                'side', // $context, choose 'normal' or 'side'
                'high', // $priority
                null 
            );
        }
        add_action( 'add_meta_boxes', 'add_banner_meta_box' );
        function show_banner_meta_box() {
            global $post;
            global $functions_file_basename;
            $meta = get_post_meta( $post->ID, 'banner', true ); 
            ?>
                <input type="hidden" name="banner_meta_box_nonce" value="<?php echo wp_create_nonce( $functions_file_basename ); ?>">
                
                <p>
                    <label for="banner[banner_type]"><?php echo __( 'Banner Type', 'bsx-wordpress' ); ?></label>
                    <br>
                    <input type="text" name="banner[banner_type]" id="banner[banner_type]" value="<?php if ( isset( $meta[ 'banner_type' ] ) ) { echo $meta[ 'banner_type' ]; } ?>" style="width: 100%;" />
                </p>
            <?php 
        }
        function save_banner_meta_box( $post_id ) {
            global $functions_file_basename;
            // verify nonce
            if ( isset( $_POST[ 'banner_meta_box_nonce' ] ) && ! wp_verify_nonce( $_POST[ 'banner_meta_box_nonce' ], $functions_file_basename ) ) {
                return $post_id;
            }
            // check autosave
            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
                return $post_id;
            }
            // check permissions
            if ( isset( $_POST['post_type'] ) && 'page' === $_POST['post_type'] ) {
                if ( ! current_user_can( 'edit_page', $post_id ) ) {
                    return $post_id;
                } 
                elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
                    return $post_id;
                }
            }
            if ( isset( $_POST[ 'banner_meta_box_nonce' ] ) ) {
                update_post_meta( $post_id, 'banner', $_POST[ 'banner' ] );
            }
        }
        add_action( 'save_post', 'save_banner_meta_box' );

    }

}