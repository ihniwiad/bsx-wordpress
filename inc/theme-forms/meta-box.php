<?php



function bsx_theme_forms_add_meta_box() {
    $screen = 'theme-forms-cpt'; // choose 'post' or 'page' or custom post
    add_meta_box( 
        'theme_forms_meta_box', // $id
        __( 'Theme Form Settings', 'bsx-wordpress' ), // $title
        'bsx_theme_forms_show_meta_box', // $callback
        $screen, // $screen
        'normal', // $context, choose 'normal' or 'side'
        'high', // $priority
        null 
    );
}
add_action( 'add_meta_boxes', 'bsx_theme_forms_add_meta_box' );
function bsx_theme_forms_show_meta_box() {
    global $post;
    global $functions_file_basename;
    $meta = get_post_meta( $post->ID, 'theme_forms', true ); 
    ?>
        <input type="hidden" name="theme_forms_meta_box_nonce" value="<?php echo wp_create_nonce( $functions_file_basename ); ?>">

        <h4><?php esc_html_e( 'Input placeholder syntax', 'bsx-wordpress' ); ?></h4>
        <p>
            <small>
                <?php esc_html_e( 'Input', 'bsx-wordpress' ); ?><code>[*my_type::my_name class="form-control" id="some-id" class="foo" data-foo="bar"]</code><br>
                <?php esc_html_e( 'Syntax', 'bsx-wordpress' ); ?><code>[*</code> required, <code>[</code> non-required, <code>my_type::</code> input type, <code>::my_name</code> name, <code> id="some-id" class="foo" data-foo="bar"]</code> attributes (optional)<br>
                <?php esc_html_e( 'Translation', 'bsx-wordpress' ); ?><code>[translate::My translatable text.]</code> (using Theme translations)<br>
            </small>
        </p>
        <h4><?php esc_html_e( 'Input examples', 'bsx-wordpress' ); ?></h4>
        <p>
            <small>
                <?php esc_html_e( 'Mandatory input example', 'bsx-wordpress' ); ?><code>[*email::email class="form-control" id="email"]</code> type: email, name: email<br>
                <?php esc_html_e( 'Optional input example', 'bsx-wordpress' ); ?><code>[text::name class="form-control" id="name"]</code> type: text, name: name<br>
                <?php esc_html_e( 'Textarea example', 'bsx-wordpress' ); ?><code>[*message::message class="form-control" id="message" rows="4"]</code><br>
                <?php esc_html_e( 'Translation example', 'bsx-wordpress' ); ?><code>[translate::E-mail]</code><br>
                <?php esc_html_e( 'Human verification display', 'bsx-wordpress' ); ?><code>[human-verification-display:: class="input-group-text"]</code><br>
                <?php esc_html_e( 'Human verification input', 'bsx-wordpress' ); ?><code>[*human-verification-input:: class="form-control" id="human-verification"]</code><br>
                <?php esc_html_e( 'Human verification refresh code attribute', 'bsx-wordpress' ); ?><code>&lt;button [human-verification-refresh-attr]&gt;[translate::Refresh code]&lt;/button&gt;</code>
            </small>
        </p>
        <h4><?php esc_html_e( 'Special input names', 'bsx-wordpress' ); ?></h4>
        <p>
            <small>
                <?php esc_html_e( 'In addition to sending an e-mail, all data is saved in the database. The following (optional) special input names are saved in separate database columns so that they can then be clearly displayed / sorted in the backend:', 'bsx-wordpress' ); ?>
                <br>
                <code>name</code>,
                <code>first_name</code>,
                <code>last_name</code>,
                <code>email</code>,
                <code>phone</code>,
                <code>company</code>,
                <code>subject</code>
            </small>
        </p>
        <?php
            if ( isset( $post->post_title ) && ! empty( $post->post_title ) ) {
            	 echo '<h4>' . __( 'Embed shortcode', 'bsx-wordpress' ) . '</h4><p><code>[theme-form id="' . $post->ID . '" title="' . $post->post_title . '"]</code></p>';
            }
        ?>

        <section class="bsxui-meta-section">
            <h3 class="bsxui-meta-heading"><?php echo __( 'Form Template', 'bsx-wordpress' ); ?></h3>
            <p class="bsxui-meta-row">
                <label class="bsxui-meta-label" for="theme_forms[form_template]"><?php echo __( 'Form Template', 'bsx-wordpress' ); ?></label>
                <br>
                <textarea class="bsxui-meta-textarea" name="theme_forms[form_template]" id="theme_forms[form_template]" rows="20" style="font-family:SFMono-Regular,Menlo,Monaco,Consolas,\'Liberation Mono\',\'Courier New\',monospace; width:100%;"><?php if ( isset( $meta[ 'form_template' ] ) ) { echo $meta[ 'form_template' ]; } ?></textarea>
            </p>
        </section>

        <section class="bsxui-meta-section">
            <h3 class="bsxui-meta-heading"><?php echo __( 'E-mail', 'bsx-wordpress' ); ?></h3>
            <p class="bsxui-meta-row">
                <label class="bsxui-meta-label" for="theme_forms[recipient_email]"><?php echo __( 'Recipient e-mail', 'bsx-wordpress' ); ?></label>
                <br>
                <input class="bsxui-meta-input" type="text" name="theme_forms[recipient_email]" id="theme_forms[recipient_email]" value="<?php if ( isset( $meta['recipient_email'] ) ) { echo $meta['recipient_email']; } ?>" style="width: 100%;"/>
            </p>
            <p class="bsxui-meta-row">
                <label class="bsxui-meta-label" for="theme_forms[sender_email]"><?php echo __( 'Sender e-mail', 'bsx-wordpress' ); ?></label>
                <br>
                <input class="bsxui-meta-input" type="text" name="theme_forms[sender_email]" id="theme_forms[sender_email]" value="<?php if ( isset( $meta['sender_email'] ) ) { echo $meta['sender_email']; } ?>" style="width: 100%;"/>
            </p>
            <?php
            	printf(
                    '<p>%s</p><p><small><code>[email]</code>, <code>[name]</code>, <code>[site-url]</code>, ...</small></p>',
                    esc_html__( 'Use placeholders in subject (optional) and e-mail templates:', 'bsx-wordpress' ),
                );
            ?>
            <p class="bsxui-meta-row">
                <label class="bsxui-meta-label" for="theme_forms[subject]"><?php echo __( 'Subject', 'bsx-wordpress' ); ?></label>
                <br>
                <input class="bsxui-meta-input" type="text" name="theme_forms[subject]" id="theme_forms[subject]" value="<?php if ( isset( $meta['subject'] ) ) { echo $meta['subject']; } ?>" style="width: 100%;"/>
            </p>
            <p class="bsxui-meta-row">
                <label class="bsxui-meta-label" for="theme_forms[email_template]"><?php echo __( 'E-mail template', 'bsx-wordpress' ); ?></label>
                <br>
                <textarea class="bsxui-meta-textarea" name="theme_forms[email_template]" id="theme_forms[email_template]" rows="12" style="font-family:SFMono-Regular,Menlo,Monaco,Consolas,\'Liberation Mono\',\'Courier New\',monospace; width:100%;"><?php if ( isset( $meta[ 'email_template' ] ) ) { echo $meta[ 'email_template' ]; } ?></textarea>
            </p>
        </section>

        <hr>

        <section class="bsxui-meta-section">
            <h3 class="bsxui-meta-heading"><?php echo __( 'E-mail 2 (optional)', 'bsx-wordpress' ); ?></h3>
            <?php
            	printf( 
                    '<p>%s</p><p><small><code>[email]</code></small></p>',
                    __( 'Optional use e-mail placeholder, e.g.:', 'bsx-wordpress' ),
                )
            ?>
            <p class="bsxui-meta-row">
                <label class="bsxui-meta-label" for="theme_forms[recipient_2_email]"><?php echo __( 'Recipient 2 e-mail', 'bsx-wordpress' ); ?></label>
                <br>
                <input class="bsxui-meta-input" type="text" name="theme_forms[recipient_2_email]" id="theme_forms[recipient_2_email]" value="<?php if ( isset( $meta['recipient_2_email'] ) ) { echo $meta['recipient_2_email']; } ?>" style="width: 100%;"/>
            </p>
            <p class="bsxui-meta-row">
                <label class="bsxui-meta-label" for="theme_forms[sender_2_email]"><?php echo __( 'Sender 2 e-mail', 'bsx-wordpress' ); ?></label>
                <br>
                <input class="bsxui-meta-input" type="text" name="theme_forms[sender_2_email]" id="theme_forms[sender_2_email]" value="<?php if ( isset( $meta['sender_2_email'] ) ) { echo $meta['sender_2_email']; } ?>" style="width: 100%;"/>
            </p>
            <?php
            	printf(
                    '<p>%s</p><p><small><code>[email]</code>, <code>[name]</code>, <code>[site-url]</code>, ...</small></p>',
                    __( 'Use placeholders in subject (optional) and e-mail templates:', 'bsx-wordpress' ),
                );
            ?>
            <p class="bsxui-meta-row">
                <label class="bsxui-meta-label" for="theme_forms[subject_2]"><?php echo __( 'Subject 2', 'bsx-wordpress' ); ?></label>
                <br>
                <input class="bsxui-meta-input" type="text" name="theme_forms[subject_2]" id="theme_forms[subject_2]" value="<?php if ( isset( $meta['subject_2'] ) ) { echo $meta['subject_2']; } ?>" style="width: 100%;"/>
            </p>
            <p class="bsxui-meta-row">
                <label class="bsxui-meta-label" for="theme_forms[email_2_template]"><?php echo __( 'E-mail 2 template', 'bsx-wordpress' ); ?></label>
                <br>
                <textarea class="bsxui-meta-textarea" name="theme_forms[email_2_template]" id="theme_forms[email_2_template]" rows="12" style="font-family:SFMono-Regular,Menlo,Monaco,Consolas,\'Liberation Mono\',\'Courier New\',monospace; width:100%;"><?php if ( isset( $meta[ 'email_2_template' ] ) ) { echo $meta[ 'email_2_template' ]; } ?></textarea>
            </p>
        </section>

    <?php 
}
function bsx_theme_forms_save_meta_box( $post_id ) {
    global $functions_file_basename;
    // verify nonce
    if ( isset( $_POST[ 'theme_forms_meta_box_nonce' ] ) && ! wp_verify_nonce( $_POST[ 'theme_forms_meta_box_nonce' ], $functions_file_basename ) ) {
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
    if ( isset( $_POST[ 'theme_forms_meta_box_nonce' ] ) ) {
        update_post_meta( $post_id, 'theme_forms', $_POST[ 'theme_forms' ] );
    }
}
add_action( 'save_post', 'bsx_theme_forms_save_meta_box' );

