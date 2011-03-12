<?php
/**
 * Plugin Name: iTunes AppStore App Rank
 * Plugin URI: http://www.paulpeelen.com/iar
 * Description: Fetches the app rank for an application in a given genre for a given country
 * Author: Paul Peelen
 * Version: 1
 * Author URI: http://www.PaulPeelen.com/
 * 
 *
 * @author Paul Peelen <Paul@PaulPeelen.com>
 * @since 11 mar 2011
 */

include_once ("class.appFetcher.php");
 
function init(){
	register_widget("iar_Widget");     
}
 
add_action("widgets_init", "init");

class iar_Widget extends WP_Widget {
	function iar_Widget () {
		$widget_ops = array( 'classname' => 'iar', 'description' => __('Fetches the app rank for an application in a given genre for a given country', 'iar') );

		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'iar' );

		$this->WP_Widget( 'iar', __('iTunes App Ranking', 'iar_Widget'), $widget_ops, $control_ops );
	}
	
	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );
		
		$oAppFetcher = new appFetcher($instance['appId'], $instance['country'], $instance['paid'], $instance['size'], $instance['genre']);
 		$aAppData	= $oAppFetcher->getAppData();
 
		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$name = $instance['name'];
		$sex = $instance['sex'];
		$show_sex = isset( $instance['show_sex'] ) ? $instance['show_sex'] : false;

		/* Before widget (defined by themes). */
		echo $before_widget;

		echo '<h2 class="widgettitle">iTunes AppStore ranking</h2>';

		if (is_array($aAppData))
		 {
		 	echo '<div style="width: 100%; text-align: center; float: none; padding: 0; margin: 0">';
		 	
		 	echo '<font style="font-size: 16px; padding-bottom: 10px; font-weight: bold;">';
		 	echo '	<a href="http://itunes.apple.com/'.$oAppFetcher->getCountryCode().'/app/id'.$oAppFetcher->getAppId().'?mt=8" target="_blank">';
		 	echo $aAppData['title'];
		 	echo '	</a>';
		 	echo '</font>';
		 	
		 	if ($instance['icon'])
		 	{
		 		echo '<a href="http://itunes.apple.com/'.$oAppFetcher->getCountryCode().'/app/id'.$oAppFetcher->getAppId().'?mt=8" target="_blank">';
		 		echo '	<img src="'.$aAppData['icon'].'" border="0" alt="'.$aAppData['title'].'" style="margin: 10px !important"/>';
		 		echo '</a>';
		 	}
		 	
			 echo '<p><b>Current position in '.$oAppFetcher->getCategoryName().' category:</b><br/></p>';
			 echo '<p><font style="font-size: 30px">' . $aAppData['position'] . '</font></p>';
			 
			 echo '</div>';
		 }
		 else {
		 	echo __("App not found. Please change range in your widget settings.", "iar");
		 }

		/* After widget (defined by themes). */
		echo $after_widget;
	}  
	
	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $new_instance;

		$instance['appId']	= (int)$instance['appId'];
		$instance['paid']	= ($instance['paid'] == "on");
		$instance['icon']	= ($instance['icon'] == "on");

		return $instance;
	}
	
	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'appId' => '411405463', 'country' => 'SE', 'genre' => '6003', 'paid' => true, 'icon' => true, 'size' => 100);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget App ID: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'appId' ); ?>"><?php _e('Apple ID:', 'iar'); ?></label>
			<input id="<?php echo $this->get_field_id( 'appId' ); ?>" name="<?php echo $this->get_field_name( 'appId' ); ?>" value="<?php echo $instance['appId']; ?>"  class="widefat" />
		</p>

		<!-- Country: Select Box -->
		<p>
			<label for="<?php echo $this->get_field_id( 'country' ); ?>"><?php _e('Country:', 'iar'); ?></label> 
			<select id="<?php echo $this->get_field_id( 'country' ); ?>" name="<?php echo $this->get_field_name( 'country' ); ?>" class="widefat" style="width:100%;">
				<option value="AR" <?php if ( $instance['country'] == "AR") echo 'selected="selected"'; ?>Argentina</option>
				<option value="AU" <?php if ( $instance['country'] == "AU") echo 'selected="selected"'; ?>>Australia</option>
				<option value="AT" <?php if ( $instance['country'] == "AT") echo 'selected="selected"'; ?>>Austria</option>
				<option value="BE" <?php if ( $instance['country'] == "BE") echo 'selected="selected"'; ?>>Belgium</option>
				<option value="BR" <?php if ( $instance['country'] == "BR") echo 'selected="selected"'; ?>>Brazil</option>
				<option value="CA" <?php if ( $instance['country'] == "CA") echo 'selected="selected"'; ?>>Canada</option>
				<option value="CL" <?php if ( $instance['country'] == "CL") echo 'selected="selected"'; ?>>Chile</option>
				<option value="CN" <?php if ( $instance['country'] == "CN") echo 'selected="selected"'; ?>>China</option>
				<option value="CO" <?php if ( $instance['country'] == "CO") echo 'selected="selected"'; ?>>Colombia</option>
				<option value="CR" <?php if ( $instance['country'] == "CR") echo 'selected="selected"'; ?>>Costa Rica</option>
				<option value="HR" <?php if ( $instance['country'] == "HR") echo 'selected="selected"'; ?>>Croatia</option>
				<option value="CZ" <?php if ( $instance['country'] == "CZ") echo 'selected="selected"'; ?>>Czech Republic</option>
				<option value="DK" <?php if ( $instance['country'] == "DK") echo 'selected="selected"'; ?>>Denmark</option>
				<option value="SV" <?php if ( $instance['country'] == "SV") echo 'selected="selected"'; ?>>El Salvador</option>
				<option value="FI" <?php if ( $instance['country'] == "FI") echo 'selected="selected"'; ?>>Finland</option>
				<option value="FR" <?php if ( $instance['country'] == "FR") echo 'selected="selected"'; ?>>France</option>
				<option value="DE" <?php if ( $instance['country'] == "DE") echo 'selected="selected"'; ?>>Germany</option>
				<option value="GR" <?php if ( $instance['country'] == "GR") echo 'selected="selected"'; ?>>Greece</option>
				<option value="GT" <?php if ( $instance['country'] == "GT") echo 'selected="selected"'; ?>>Guatemala</option>
				<option value="HK" <?php if ( $instance['country'] == "HK") echo 'selected="selected"'; ?>>Hong Kong</option>
				<option value="HU" <?php if ( $instance['country'] == "HU") echo 'selected="selected"'; ?>>Hungary</option>
				<option value="IN" <?php if ( $instance['country'] == "IN") echo 'selected="selected"'; ?>>India</option>
				<option value="ID" <?php if ( $instance['country'] == "ID") echo 'selected="selected"'; ?>>Indonesia</option>
				<option value="IE" <?php if ( $instance['country'] == "IE") echo 'selected="selected"'; ?>>Ireland</option>
				<option value="IL" <?php if ( $instance['country'] == "IL") echo 'selected="selected"'; ?>>Israel</option>
				<option value="IT" <?php if ( $instance['country'] == "IT") echo 'selected="selected"'; ?>>Italy</option>
				<option value="JP" <?php if ( $instance['country'] == "JP") echo 'selected="selected"'; ?>>Japan</option>
				<option value="KR" <?php if ( $instance['country'] == "KR") echo 'selected="selected"'; ?>>Korea, Republic Of</option>
				<option value="KW" <?php if ( $instance['country'] == "KW") echo 'selected="selected"'; ?>>Kuwait</option>
				<option value="LB" <?php if ( $instance['country'] == "LB") echo 'selected="selected"'; ?>>Lebanon</option>
				<option value="LU" <?php if ( $instance['country'] == "LU") echo 'selected="selected"'; ?>>Luxembourg</option>
				<option value="MY" <?php if ( $instance['country'] == "MY") echo 'selected="selected"'; ?>>Malaysia</option>
				<option value="MX" <?php if ( $instance['country'] == "MX") echo 'selected="selected"'; ?>>Mexico</option>
				<option value="NL" <?php if ( $instance['country'] == "NL") echo 'selected="selected"'; ?>>Netherlands</option>
				<option value="NZ" <?php if ( $instance['country'] == "NZ") echo 'selected="selected"'; ?>>New Zealand</option>
				<option value="NO" <?php if ( $instance['country'] == "NO") echo 'selected="selected"'; ?>>Norway</option>
				<option value="PK" <?php if ( $instance['country'] == "PK") echo 'selected="selected"'; ?>>Pakistan</option>
				<option value="PA" <?php if ( $instance['country'] == "PA") echo 'selected="selected"'; ?>>Panama</option>
				<option value="PE" <?php if ( $instance['country'] == "PE") echo 'selected="selected"'; ?>>Peru</option>
				<option value="PH" <?php if ( $instance['country'] == "PH") echo 'selected="selected"'; ?>>Philippines</option>
				<option value="PL" <?php if ( $instance['country'] == "PL") echo 'selected="selected"'; ?>>Poland</option>
				<option value="PT" <?php if ( $instance['country'] == "PT") echo 'selected="selected"'; ?>>Portugal</option>
				<option value="QA" <?php if ( $instance['country'] == "QA") echo 'selected="selected"'; ?>>Qatar</option>
				<option value="RO" <?php if ( $instance['country'] == "RO") echo 'selected="selected"'; ?>>Romania</option>
				<option value="RU" <?php if ( $instance['country'] == "RU") echo 'selected="selected"'; ?>>Russia</option>
				<option value="SA" <?php if ( $instance['country'] == "SA") echo 'selected="selected"'; ?>>Saudi Arabia</option>
				<option value="SG" <?php if ( $instance['country'] == "SG") echo 'selected="selected"'; ?>>Singapore</option>
				<option value="SK" <?php if ( $instance['country'] == "SK") echo 'selected="selected"'; ?>>Slovakia</option>
				<option value="SI" <?php if ( $instance['country'] == "SI") echo 'selected="selected"'; ?>>Slovenia</option>
				<option value="ZA" <?php if ( $instance['country'] == "ZA") echo 'selected="selected"'; ?>>South Africa</option>
				<option value="ES" <?php if ( $instance['country'] == "ES") echo 'selected="selected"'; ?>>Spain</option>
				<option value="LK" <?php if ( $instance['country'] == "LK") echo 'selected="selected"'; ?>>Sri Lanka</option>
				<option value="SE" <?php if ( $instance['country'] == "SE") echo 'selected="selected"'; ?>>Sweden</option>
				<option value="CH" <?php if ( $instance['country'] == "CH") echo 'selected="selected"'; ?>>Switzerland</option>
				<option value="TW" <?php if ( $instance['country'] == "TW") echo 'selected="selected"'; ?>>Taiwan</option>
				<option value="TH" <?php if ( $instance['country'] == "TH") echo 'selected="selected"'; ?>>Thailand</option>
				<option value="TR" <?php if ( $instance['country'] == "TR") echo 'selected="selected"'; ?>>Turkey</option>
				<option value="GB" <?php if ( $instance['country'] == "GB") echo 'selected="selected"'; ?>>UK</option>
				<option value="US" <?php if ( $instance['country'] == "US") echo 'selected="selected"'; ?>>USA</option>
				<option value="AE" <?php if ( $instance['country'] == "AE") echo 'selected="selected"'; ?>>United Arab Emirates</option>
				<option value="VE" <?php if ( $instance['country'] == "VE") echo 'selected="selected"'; ?>>Venezuela</option>
				<option value="VN" <?php if ( $instance['country'] == "VN") echo 'selected="selected"'; ?>>Vietnam</option>
			</select>
		</p>

		<!-- Genre: Select Box -->
		<p>
			<label for="<?php echo $this->get_field_id('genre'); ?>"><?php _e('Genre:', 'iar'); ?></label> 
			<select id="<?php echo $this->get_field_id('genre'); ?>" name="<?php echo $this->get_field_name('genre'); ?>" class="widefat" style="width:100%;">
				<option value="0" <?php if ( $instance['genre'] == "0") echo 'selected="selected"'; ?>>All Genres</option>
				<option value="6018" <?php if ( $instance['genre'] == "6018") echo 'selected="selected"'; ?>>Books</option>
				<option value="6000" <?php if ( $instance['genre'] == "6000") echo 'selected="selected"'; ?>>Business</option>
				<option value="6017" <?php if ( $instance['genre'] == "6017") echo 'selected="selected"'; ?>>Education</option>
				<option value="6016" <?php if ( $instance['genre'] == "6016") echo 'selected="selected"'; ?>>Entertainment</option>
				<option value="6015" <?php if ( $instance['genre'] == "6015") echo 'selected="selected"'; ?>>Finance</option>
				<option value="6014" <?php if ( $instance['genre'] == "6014") echo 'selected="selected"'; ?>>Games</option>
				<option value="6013" <?php if ( $instance['genre'] == "6013") echo 'selected="selected"'; ?>>Healthcare &amp; Fitness</option>
				<option value="6012" <?php if ( $instance['genre'] == "6012") echo 'selected="selected"'; ?>>Lifestyle</option>
				<option value="6020" <?php if ( $instance['genre'] == "6020") echo 'selected="selected"'; ?>>Medical</option>
				<option value="6011" <?php if ( $instance['genre'] == "6011") echo 'selected="selected"'; ?>>Music</option>
				<option value="6010" <?php if ( $instance['genre'] == "6010") echo 'selected="selected"'; ?>>Navigation</option>
				<option value="6009" <?php if ( $instance['genre'] == "6009") echo 'selected="selected"'; ?>>News</option>
				<option value="6008" <?php if ( $instance['genre'] == "6008") echo 'selected="selected"'; ?>>Photography</option>
				<option value="6007" <?php if ( $instance['genre'] == "6007") echo 'selected="selected"'; ?>>Productivity</option>
				<option value="6006" <?php if ( $instance['genre'] == "6006") echo 'selected="selected"'; ?>>Reference</option>
				<option value="6005" <?php if ( $instance['genre'] == "6005") echo 'selected="selected"'; ?>>Social Networking</option>
				<option value="6004" <?php if ( $instance['genre'] == "6004") echo 'selected="selected"'; ?>>Sports</option>
				<option value="6003" <?php if ( $instance['genre'] == "6003") echo 'selected="selected"'; ?>>Travel</option>
				<option value="6002" <?php if ( $instance['genre'] == "6002") echo 'selected="selected"'; ?>>Utilities</option>
				<option value="6001" <?php if ( $instance['genre'] == "6001") echo 'selected="selected"'; ?>>Weather</option>
			</select>
		</p>

		<!-- Size: Select Box -->
		<p>
			<label for="<?php echo $this->get_field_id('size'); ?>"><?php _e('Check range (the higher range, the longer it takes):', 'iar'); ?></label> 
			<select id="<?php echo $this->get_field_id('size'); ?>" name="<?php echo $this->get_field_name('size'); ?>" class="widefat" style="width:100%;">
				<option value="10" <?php if ( $instance['size'] == "0") echo 'selected="selected"'; ?>>10</option>
				<option value="25" <?php if ( $instance['size'] == "25") echo 'selected="selected"'; ?>>25</option>
				<option value="50" <?php if ( $instance['size'] == "50") echo 'selected="selected"'; ?>>50</option>
				<option value="75" <?php if ( $instance['size'] == "75") echo 'selected="selected"'; ?>>75</option>
				<option value="100" <?php if ( $instance['size'] == "100") echo 'selected="selected"'; ?>>100</option>
				<option value="150" <?php if ( $instance['size'] == "150") echo 'selected="selected"'; ?>>150</option>
				<option value="200" <?php if ( $instance['size'] == "200") echo 'selected="selected"'; ?>>200</option>
				<option value="250" <?php if ( $instance['size'] == "250") echo 'selected="selected"'; ?>>250</option>
				<option value="300" <?php if ( $instance['size'] == "300") echo 'selected="selected"'; ?>>300</option>
			</select>
		</p>
		
		<!-- Paid app? Checkbox -->
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['paid'], true ); ?> id="<?php echo $this->get_field_id('paid'); ?>" name="<?php echo $this->get_field_name('paid'); ?>" /> 
			<label for="<?php echo $this->get_field_id('paid'); ?>"><?php _e('Paid application?', 'iar'); ?></label>
		</p>
		
		<!-- Icon? Checkbox -->
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['icon'], true ); ?> id="<?php echo $this->get_field_id('icon'); ?>" name="<?php echo $this->get_field_name('icon'); ?>" /> 
			<label for="<?php echo $this->get_field_id('icon'); ?>"><?php _e('Show icon?', 'iar'); ?></label>
		</p>

	<?php
	}
}




?>
