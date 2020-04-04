<?php
   /*
   Plugin Name: WpJupyter
   Description: A plugin to insert and display jupyter generated html content
   Version: 1.0
   Author: Juan M. Tirado
   Author URI: http://www.jmtirado.net
   License: Apache 2.0
   */

// based on https://eng.aurelienpierre.com/2018/05/make-jupyter-notebooks-easy-to-blog-in-wordpress/

// Include Plotly and Mathjax to display Latex.

wp_register_script('plotly', "//cdn.plot.ly/plotly-latest.min.js", array(), null, true);
wp_register_script( 'mathjax', "//cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS_HTML", array(), null, true);

function load_maths( $content) {
    if (strpos($content, '$') !== false || strpos($content, '[latex]') !== false) {
        wp_enqueue_script( 'plotly' );
        wp_enqueue_script( 'mathjax' );
    }
    
    return $content;
}

add_filter( 'the_content', 'load_maths' );

function my_custom_js() {
    echo "<script type='text/x-mathjax-config'>
    MathJax.Hub.Config({
        tex2jax: {
            inlineMath: [ ['$','$'], ['[latex]', '[/latex]']],
            displayMath: [ ['$$','$$'] ],
            processEscapes: true,
            processEnvironments: true
        },
        // Center justify equations in code and markdown cells. Elsewhere
        // we use CSS to left justify single line equations in code cells.
        displayAlign: 'center',
        'HTML-CSS': {
            styles: {'.MathJax_Display': {'margin': 0}},
            linebreaks: { automatic: true },
        extensions: ['handle-floats.js'],
        availableFonts: ['STIX','TeX'],
            preferredFont: 'STIX',
        webFont:'STIX-Web'
        },
    });
    </script>";
}

// Add hook for front-end <head></head>
add_action('wp_head', 'my_custom_js');


// Include HTML
function include_shortcode( $atts ) {
    // Attributes
    $atts = shortcode_atts(
        array('id' => ''),
        $atts,
        'include'
    );
 
    $uploads = wp_upload_dir();
 
        // Enqueue maths scripts on HTML includes
        wp_enqueue_script( 'plotly' );
        wp_enqueue_script( 'mathjax' );
    ob_start();
    include $uploads["basedir"].'/'.trim($atts['id']);
    return ob_get_clean();
 
}


function wpjupyter_enqueue_style() {
    wp_enqueue_style( 'NbConvert', plugins_url( '/css/wpjupyter.css', __FILE__ ));
}
// Add the CSS
add_action( 'wp_enqueue_scripts', 'wpjupyter_enqueue_style' );
// Add the shortcut
add_shortcode( 'notebook', 'include_shortcode' );

