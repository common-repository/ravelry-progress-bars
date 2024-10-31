<?php
/*
Plugin Name: Ravelry Progress Bars
Plugin URI: http://amplifiedprojects.com/projects/ravelry-plugin
Description: A plugin to display progress bars for your current WIP
Version: 1.0
Author: Amanda Chappell
Author URI: http://amplifiedprojects.com
*/

/*  Copyright 2009  Amanda Chappell  (email : amanda@amplifiedprojects.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/



add_filter('wp_print_scripts', 'ravelry');

function ravelry()
{
$options = get_option("widget_RavelryPB");
echo '<style type="text/css" media="screen">
        
          .rav_project {
            margin-bottom: 5px;
          }
          
          .rav_project a.rav_title {
            font-size: .9em;
          }
          
          .rav_project .rav_progress_text {
            position: relative;
            text-align: center;
          }
        
          .rav_project .rav_progress_bar_wrapper {
            border: 1px solid transparent;
            margin-top: 2px;
          } 
        
          .rav_project .rav_progress_bar { 
            position: relative;
            padding: 1px;
            border-left: 2px solid #aaa;
            border-top: 2px solid #aaa;
            border-bottom: 2px solid #ccc;
            border-right: 2px solid #ccc;
            background-color: transparent;
          }
        
          .rav_project .rav_progress_bar .rav_progress_filled { 
            position: absolute;
          }
          
          .rav_project .rav_photo_link {
            margin-bottom: 5px;
            display: block;
            width: 77px;
            height: 77px;
            margin-left: 1px;
            margin-top: 5px;
            border: 1px solid transparent;
          }
          
          .rav_project .rav_photo_link img {
            width: 75px;
            height: 75px;
            border: 1px solid #ffffff;
          }
          
          .rav_project_with_photos {
            margin-bottom: 20px;
          }
          
        </style>
        
        <script type="text/javascript" charset="utf-8">
          RavelryThing = function() {
            var progressData = null;
            var $ = function(id) { return document.getElementById(id); };
        
            var $E = function(data) {
                var el;
                if (\'string\' == typeof data) {
                  el = document.createTextNode(data);
                } else {
                  el = document.createElement(data.tag);
                  delete(data.tag);
                  if (\'undefined\' != typeof data.children) {
                    for (var i=0, child=null; \'undefined\' != typeof (child=data.children[i]); i++) { if (child) { el.appendChild($E(child)); } }
                    delete(data.children);
                  }
                  for (attr in data) { 
                    if (attr == \'style\') {
                      for (s in data[attr]) {
                        el.style[s] =  data[attr][s];
                      } 
                    } else if (data[attr]) {
                      el[attr]=data[attr]; 
                    }
                  }
                }
                return el;
            };
            
            return {
              progressReceived: function(data) {
                progressData = data;
              },
        
              drawProgressBars: function(options) {
                if (!progressData) return;
                
                if (!options) options = {};
                if (\'number\' == typeof options.height) options.height += \'px\';
                if (!options.height) options.height = \'1.3em\';
                if (!options.width) options.width = ';
echo $options['width'];
         echo   ';
		
		if (!options.color) options.color = \'';
echo $options['barcolor'];
echo '\';
                if (!options.container) options.container = \'rav_progress_bars\';
                
                var container = $(options.container);
                if (!container) {
                  document.write("\u003cdiv id=\'" + options.container + "\'\u003e\u003c/div\u003e");
                  container = $(options.container);
                }
                
                var selectedProjects = progressData.projects;
                if (options.projects) {
                  var projectsById = new Object();
                  for (var i=0; i < selectedProjects.length; i++) {
                    projectsById[selectedProjects[i].permalink] = selectedProjects[i];
                  }
                  selectedProjects = new Array();
                  for (var i=0; i < options.projects.length; i++) {
                    var project = projectsById[options.projects[i]];
                    if (project) {
                      selectedProjects.push(project);
                    }
                  }
                }
                
                for (var i=0; i < selectedProjects.length; i++) {
                  var project = selectedProjects[i];
                  var filledStyle = { width: Math.round((project.progress/100) * options.width) + \'px\', height: options.height, backgroundColor: options.color};
                  var barStyle = { width: (options.width) + \'px\', height: options.height};
                  var className = \'rav_project\'
                  
                  var photo = null;
                  if (options.photos && project.thumbnail) {
                    className += \' rav_project_with_photos\';
                    photo = { tag: \'div\', children:[
			{ tag: \'a\', className: \'rav_photo_link\', href: project.thumbnail.flickrUrl, children: [
                        	{tag: \'img\', src: project.thumbnail.src }
                      ]}]
                    };
                  }
                  
                  var title = null;
                  if (options.title != false) {
                    title = { tag: \'a\', className: \'rav_title\', href: project.url, children: [project.name] };
                  }
                  
                  container.appendChild($E({
                    tag: \'div\',
                    className: className,
                    children: [ title, photo,
                      { tag: \'div\', className: \'rav_progress_bar_wrapper\', style: barStyle, children: [
                        { tag: \'div\', className: \'rav_progress_bar\', style: barStyle, children: [
                          {tag: \'div\', className: \'rav_progress_filled\', style: filledStyle},
                          {tag: \'div\', className: \'rav_progress_text\', style: barStyle, 
                            children: [ project.progress + \'%\' ]}
                        ]}
                      ]}
                    ]
                  }));
                }
              }
            }
          }();
        </script>
        <script src="http://api.ravelry.com/projects/';
	
	echo $options['username'];
	echo '/progress.json?callback=RavelryThing.progressReceived&key=';
	echo $options['api'];
	echo '&version=0';
	if($options['ip']=='in-progress'){
		echo '&status=in-progress';
		if($options['h']=='hibernating'){
			echo '+hibernating';
			if($options['fi']=='finished'){
				echo '+finished';
				if($options['fr']=='frogged'){
					echo '+frogged';
				}
			}
			else{
				if($options['fr']=='frogged'){
					echo '+frogged';
				}
			}
		}
		else{
			if($options['fi']=='finished'){
				echo '+finished';
				if($options['fr']=='frogged'){
					echo '+frogged';
				}
			}
			else{
				if($options['fr']=='frogged'){
					echo '+frogged';
				}
			}
		}
	}
	else{
		if($options['h']=='hibernating'){
			echo 'hibernating';
			if($options['fi']=='finished'){
				echo '+finished';if($options['fr']=='frogged'){
					echo '+frogged';
				}
				
			}
			else{
				if($options['fr']=='frogged'){
					echo '+frogged';
				}
			}
		}
		else{
			if($options['fi']=='finished'){
				echo 'finished';
				if($options['fr']=='frogged'){
					echo '+frogged';
				}
			}
			else{
				if($options['fr']=='frogged'){
					echo 'frogged';
				}
			}
		}
	}
	if($options['notes']=='notes'){
		echo '&notes=true';
	}
	echo '"></script>';
        
}

function widget_RavelryPB($args) 
{
	extract($args);
	echo $before_widget;
	$options = get_option("widget_RavelryPB");

	echo $before_title;
	echo $options['title']; 
	echo $after_title;

	
?>
	<script>  RavelryThing.drawProgressBars(<?php if($options['photo'] == 'Photo'){ echo '{photos: true}';}?>); </script>  
<?php

	echo $after_widget;

}

function ravelryPB_control()
{
	$options = get_option("widget_RavelryPB");

 	if (!is_array( $options ))
	{
		$options = array('title' => 'My Widget Title','username' => 'user','api' => 'key','width' => '100', 'barcolor' => 'lightgreen', 'photo' => '', 'ip' => '', 'h' => '', 'fi' => '', 'fr' => '', 'notes' => '');
  	}  

	if($_POST['ravelryPB-Submit']){
		$options['title'] = htmlspecialchars($_POST['ravelryPB-WidgetTitle']);
		$options['username'] = htmlspecialchars($_POST['ravelryPB-WidgetUsername']);
		$options['api'] = htmlspecialchars($_POST['ravelryPB-WidgetAPI']); 
		
		$options['width'] = htmlspecialchars($_POST['ravelryPB-WidgetWidth']);
		
		$options['barcolor'] = htmlspecialchars($_POST['ravelryPB-WidgetBarColor']);
		$options['photo'] = htmlspecialchars($_POST['ravelryPB-WidgetPhoto']);
		$options['ip'] = htmlspecialchars($_POST['ravelryPB-WidgetProjectTypeIP']);
		$options['h'] = htmlspecialchars($_POST['ravelryPB-WidgetProjectTypeH']);
		$options['fi'] = htmlspecialchars($_POST['ravelryPB-WidgetProjectTypeFi']);
		$options['fr'] = htmlspecialchars($_POST['ravelryPB-WidgetProjectTypeFr']);
		$options['notes'] = htmlspecialchars($_POST['ravelryPB-WidgetNotes']);
		update_option("widget_RavelryPB", $options);
	}

	echo '<p>
			<label for="ravelryPB-WidgetTitle">Widget Title: </label>
			<input type="text" id="ravelryPB-WidgetTitle" name="ravelryPB-WidgetTitle" value="';
	echo $options['title'];
	echo '" />
			<input type="hidden" id="ravelryPB-Submit" name="ravelryPB-Submit" value="1" />
		</p>
		<p>
			<label for="ravelryPB-WidgetUsername">Username: </label>
			<input type="text" id="ravelryPB-WidgetUsername" name="ravelryPB-WidgetUsername" value="';
	echo $options['username'];
	echo '" />
			<input type="hidden" id="ravelryPB-Submit" name="ravelryPB-Submit" value="1" />
		</p>
		<p>
			<label for="ravelryPB-WidgetAPI">API Key: </label>
			<input type="text" id="ravelryPB-WidgetAPI" name="ravelryPB-WidgetAPI" value="';
	echo $options['api'];
	echo '" />
			<input type="hidden" id="ravelryPB-Submit" name="ravelryPB-Submit" value="1" />
		</p>
		
		<p>
			
			<label for="ravelryPB-WidgetWidth">Bar Width: </label>
			
			<input type="text" id="ravelryPB-WidgetWidth" name="ravelryPB-WidgetWidth" value="';
	
	echo $options['width'];
	echo '" />
			
			<input type="hidden" id="ravelryPB-Submit" name="ravelryPB-Submit" value="1" />
		
		</p>
		
		<p>
			
			<label for="ravelryPB-WidgetBarColor">Bar Color: </label>
			
			<input type="text" id="ravelryPB-WidgetBarColor" name="ravelryPB-WidgetBarColor" value="';
	
	echo $options['barcolor'];
	
	echo '" />
			
			<input type="hidden" id="ravelryPB-Submit" name="ravelryPB-Submit" value="1" />
		
		</p>
		<p>
		
			<label for="ravelryPB-WidgetPhoto">Photos? </label>			
			<input type="checkbox" id="ravelryPB-WidgetPhoto" name="ravelryPB-WidgetPhoto" value="Photo" ';
	if($options['photo'] == 'Photo'){
		echo 'checked';
	}
	echo '" />
			
			<input type="hidden" id="ravelryPB-Submit" name="ravelryPB-Submit" value="1" />
		
		</p>
		<p>
			<label for="ravelryPB-WidgetNotes">Notes?</label>
			<input type="checkbox" id="ravelryPB-WidgetNotes" name="ravelryPB-WidgetNotes" value="notes" ';
	if($options['notes'] == 'notes'){
		echo 'checked';
	}
	echo '" />
			
			<input type="hidden" id="ravelryPB-Submit" name="ravelryPB-Submit" value="1" />
		
		</p>
		<p>
		
			<label for="ravelryPB-WidgetProjectType">Project Types </label>		
		</p>	
		<p>
			<label for="ravelryPB-WidgetProjectTypeIP">In-Progress</label>
			<input type="checkbox" id="ravelryPB-WidgetProjectTypeIP" name="ravelryPB-WidgetProjectTypeIP" value="in-progress" ';
	if($options['ip'] == 'in-progress'){
		echo 'checked';
	}
	echo '" />
			
			<input type="hidden" id="ravelryPB-Submit" name="ravelryPB-Submit" value="1" />
		
		</p>
		<p>
			<label for="ravelryPB-WidgetProjectTypeH">Hibernating</label>
			<input type="checkbox" id="ravelryPB-WidgetProjectTypeH" name="ravelryPB-WidgetProjectTypeH" value="hibernating" ';
	if($options['h'] == 'hibernating'){
		echo 'checked';
	}
	echo '" />
			
			<input type="hidden" id="ravelryPB-Submit" name="ravelryPB-Submit" value="1" />
		
		</p>
		<p>
			<label for="ravelryPB-WidgetProjectTypeFi">Finished</label>
			<input type="checkbox" id="ravelryPB-WidgetProjectTypeFi" name="ravelryPB-WidgetProjectTypeFi" value="finished" ';
	if($options['fi'] == 'finished'){
		echo 'checked';
	}
	echo '" />
			
			<input type="hidden" id="ravelryPB-Submit" name="ravelryPB-Submit" value="1" />
		
		</p>
		<p>
			<label for="ravelryPB-WidgetProjectTypeFr">Frogged</label>
			<input type="checkbox" id="ravelryPB-WidgetProjectTypeFr" name="ravelryPB-WidgetProjectTypeFr" value="frogged" ';
	if($options['fr'] == 'frogged'){
		echo 'checked';
	}
	echo '" />
			
			<input type="hidden" id="ravelryPB-Submit" name="ravelryPB-Submit" value="1" />
		
		</p>';

}

function ravelryPB_init()
{
  	register_sidebar_widget(__('Ravelry Progress Bars'), 'widget_RavelryPB');
  	register_widget_control('Ravelry Progress Bars','ravelryPB_control');
}

add_action("plugins_loaded", "ravelryPB_init");

?>