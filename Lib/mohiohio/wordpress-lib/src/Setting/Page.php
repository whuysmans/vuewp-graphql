<?php
namespace Mohiohio\WordPress\Setting;

class Page
{
    function __construct(Array $args)
    {
        $args += [
            'setting' => '__required',
            'setting_fields' => '__required',
            'display_name' => null,
            'header' => null,
            'intro' => null,
            'page_callback' => null,
            'capability' => 'manage_options'
        ];

        foreach($args as $k => $v) {
            if($v === '__required'){
                throw new \Exception('Missing argument '.$k);
            }
        }

        extract($args);

        add_action('admin_menu', function() use($display_name, $setting, $page_callback, $capability, $intro, $header) {

            add_options_page($display_name, $display_name, $capability, $setting, function() use ($page_callback, $setting, $display_name, $intro, $header) {

                if($page_callback)
                    $page_callback();
                else { ?>
                    <div class="wrap">
                            <?php if($header):?><?php echo $header?><?php else:?><h2><?php echo $display_name?></h2><?php endif;?>
                            <?php if($intro): ?>
                                <?=$intro;?>
                            <?php endif;?>
                            <form method="post" action="options.php">
                            <?php
                                settings_fields($setting);
                                do_settings_sections($setting);
                                submit_button();
                            ?>
                            </form>
                    </div>
<?php
                }
            });
        });

        add_action('admin_init', function() use($setting, $setting_fields, $display_name){

            register_setting( $setting, $setting );

            foreach($setting_fields as $field) {

                $field->get_section()->add();

                add_settings_field($field->name, $field->title, $field->get_display_callback($setting), $setting, $field->get_section()->get_id());
            }
        });

    }
}
