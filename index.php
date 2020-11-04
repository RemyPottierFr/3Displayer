<?php

/**
 * @package 3Displayer
 * @version 0.0.1
 */
/*
Plugin Name: 3Displayer
Plugin URI: http://stratandgrowth.com/photogrammetrie/
Description: Plugin pour l'affichage de modèles 3D
Author: Rémy Pottier
Version: 0.0.1
Author URI: https://www.malt.fr/profile/remypottier
*/

include 'shortcodes.php';

function my_admin_page_contents()
{
?>
  <main id="page">
    <div>
      <h1 class="page__title">
        Generate a shortcode
      </h1>
      <p class="page__desc">
        Lorem ipsum dolor sit amet consectetur adipisicing elit. Ab deleniti quos aspernatur nobis veniam nam distinctio, cumque, cum similique dolores ex voluptatum quisquam accusamus repudiandae atque necessitatibus neque autem? Ab sunt modi repellat quia praesentium aliquam dolor laboriosam consectetur blanditiis temporibus! Et dolor, adipisci ipsa esse facilis ut eligendi quidem?
      </p>
    </div>
    <section class="page__content">
      <form action="#" method="POST" class="page__form">
        <div class="page__form-block">
          <label class="page__form-label">
            Model name :
            <input required type="text" name="model_name" class="page__form-input" placeholder="Link from media galery" />
          </label>
          <label class="page__form-label">
            Model size :
            <input required type="number" name="model_size" class="page__form-input" min=0 placeholder="Use to calculate camera place" />
          </label>
        </div>
        <div class="page__form-block">
          <label class="page__form-label">
            Model rotation :
            <input type="checkbox" id="rotation_speed_checkbox" name="rotation" class="page__form-input" />
          </label>
          <label class="page__form-label">
            Rotation speed :
            <input type="number" name="rotation_speed" id="rotation_speed_input" disabled="true" value="1" min=1 max=100 class="page__form-input" />
          </label>
          <label class="page__form-label">
            Model scale :
            <input required type="number" name="model_scale" value="1" min=1 max=100 class="page__form-input" />
          </label>
        </div>
        <div class="page__form-block">
          <label class="page__form-label">
            Scene light power :
            <input required type="number" name="light_power" min=1 max=100 value=1 class="page__form-input" />
          </label>
          <label class="page__form-label">
            Fullscreen background color:
            <input required type="color" name="fullscreen_color" value="#ffffff" class="page__form-input" />
          </label>
        </div>
        <button type="submit" class="page__form-submit">Generate</button>
        <?php if (isset($_POST['model_name'])) : ?>
          <div class="shortcode__result">
            <h1>Copy this on your page and it's work !</h1>
            <h2 class="shortcode__result-code">[3Displayer light_power=<?php echo $_POST['light_power'] ?> model_size=<?php echo $_POST['model_size'] ?> model_name="<?php echo $_POST['model_name'] ?>" model_speed=<?php if (isset($_POST['rotation_speed'])) {
                                                                                                                                                                                                                        echo isset($_POST['rotation_speed']);
                                                                                                                                                                                                                      } else {
                                                                                                                                                                                                                        echo 0;
                                                                                                                                                                                                                      } ?> model_scale=<?php echo $_POST['model_scale'] ?> fullscreen_color="<?php echo $_POST['fullscreen_color'] ?>"]</h2>
          </div>
        <?php endif; ?>
      </form>
    </section>
  </main>
<?php
}

function my_admin_menu()
{
  add_menu_page(
    'Create shortcode',
    '3Displayer',
    'manage_options',
    '3Displayer',
    'my_admin_page_contents',
    'dashicons-schedule',
    3
  );
}

add_action('admin_menu', 'my_admin_menu');

function script_css()
{
  wp_enqueue_style('indexCss', plugins_url('/css/index.css', __FILE__));
}
add_action('admin_print_styles', 'script_css');

add_action('admin_footer', 'my_action_javascript'); // Write our JS below here

function my_action_javascript()
{ ?>
  <script type="text/javascript">
    const checkboxRotation = document.getElementById('rotation_speed_checkbox');
    const inputRotation = document.getElementById('rotation_speed_input');
    checkboxRotation && (checkboxRotation.onchange = function(event) {
      event.target.checked ? (inputRotation.disabled = false) : (inputRotation.disabled = true);
    })
  </script> <?php
          }
