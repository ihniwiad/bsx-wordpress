<?php

?>

<div data-id="form-wrapper">

    <form id="ajax-contact" method="post" action="<?php echo get_bloginfo( 'url' ); ?>/wp-json/bsx/v1/mailer/" data-fn="mail-form">
        <div class="form-group">
            <label for="name">Name</label>
            <input class="form-control" type="text" id="name" name="name__text__r" required>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">Please fill this field.</div>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input class="form-control" type="email" id="email" name="email__email__r" aria-describedby="emailHelp" required>
            <div class="invalid-feedback">Please provide a valid e-mail.</div>
            <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
        </div>

        <div class="form-group">
            <label for="number">Number</label>
            <input class="form-control" type="number" id="number" name="number__number__r" required>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">Please fill this field.</div>
        </div>

        <div class="form-group">
            <label for="optional">Optional</label>
            <input class="form-control" type="text" id="optional" name="optional__text">
        </div>

        <div class="form-group">
            <label for="message">Message</label>
            <textarea class="form-control" id="message" name="message__longtext__r" rows="4" required></textarea>
            <div class="invalid-feedback">Please fill this field.</div>
        </div>

        <hr>

        <div class="form-group">
            <label for="subject">Subject (config)</label>
            <input class="form-control text-monospace" type="text" id="subject" name="subject__text__r" required>
            <div class="invalid-feedback">Please fill this field.</div>
        </div>

        <div class="form-group">
            <label for="template">Template (config)</label>
            <textarea class="form-control text-monospace" id="template" name="template__longtext__r" rows="8" required></textarea>
            <div class="invalid-feedback">Please fill this field.</div>
        </div>

        <div class="form-group">
            <button class="btn btn-outline-primary" type="submit">Send</button>
        </div>
    </form>

    <div data-g-tg="message" aria-hidden="true" style="display: none;">
        <div class="alert alert-success lead mb-4" role="alert">
            <span class="fa fa-check fa-lg" aria-hidden="true"></span> <?php echo esc_html__( 'Your message has been successfully sent.', 'bsx-wordpress' ); ?>
        </div>
        <div data-g-tg="response-text">
        </div>
    </div>

</div>