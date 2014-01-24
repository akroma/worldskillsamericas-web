<?php require("aggregator/setup.inc.php"); ?>
<?php 
  session_start();
  $login = false;
  $login_error = false;

  if(isset($_GET['logout'])){
    session_unset($_SESSION['ag_login']);
  }

  if(isset($_SESSION['ag_login']) && $_SESSION['ag_login'] === true){
    $login = true;
  }
  else if(isset($_POST['ag_login']) && $_POST['ag_login'] == 'wsiaggregator123'){
    $login = true;
    $_SESSION['ag_login'] = true;
  }
  else if(isset($_POST['ag_login'])){
    $login = false;
    $login_error = true;
  }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>WorldSkills Aggregator</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="WorldSkills Aggregator">
    <meta name="author" content="Joni Aaltonen, WorldSkills International">

    <!-- Le styles -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <style>
      body {
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
      }
    </style>
    <link href="assets/css/bootstrap-responsive.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="assets/js/html5shiv.js"></script>
    <![endif]-->

    <!-- Fav and touch icons -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/ico/apple-touch-icon-114-precomposed.png">
      <link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/ico/apple-touch-icon-72-precomposed.png">
                    <link rel="apple-touch-icon-precomposed" href="assets/ico/apple-touch-icon-57-precomposed.png">
                                   <link rel="shortcut icon" href="assets/ico/favicon.png">
  </head>
    <script src="assets/js/jquery.js"></script>
    <?php if($login): ?>
  <script type='text/javascript'>
    $(document).ready(function(){
      $('.nav li a').click(function(event){
        event.preventDefault();
        var url = this.href;

        if(this.id == 'logout'){
          document.location = 'index.php?logout';
        }
        else{
        
        $('.bodyContainer').fadeOut(500, function(){ 
            $('.bodyContainer').html("Loading, please wait...");
            $(this).fadeIn(500, function(){
              $('.bodyContainer').load(url);//, false, function(){ $(this).fadeOut(500, function(){ $(this).fadeIn(); }) });
            });
          });
      }//else


      });
    });
  </script>
<?php endif; ?>

  <body>

    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="brand" href="#">WorldSkills Aggregator</a>
          <?php if($login): ?>
          <div class="nav-collapse collapse">
            <ul class="nav">
              <li><a href="aggregator/data-full.php?v">Full</a></li>
              <li><a href="aggregator/data-generic.php?v">Generic</a></li>
              <li><a href="aggregator/data-news.php?v">News</a></li>
              <li><a href="aggregator/data-event.php?v">Event</a></li>
              <li><a href="aggregator/data-venue.php?v">Venue</a></li>
              <li><a href="aggregator/data-sponsors.php?v">Sponsors</a></li>
              <li><a href="aggregator/data-photos.php?v">Photos</a></li>
              <li><a href="aggregator/data-videos.php?v">Videos</a></li>
              <li><a href="aggregator/data-local-push.php?v">Local Push</a></li>
              <li><a href="aggregator/data-all-skills.php?v">All Skills</a></li>
              <li><a href="aggregator/data-skill-specific.php?skill_number=17&amp;v">Skill</a></li>              
              <li><a href="aggregator/data-sectors.php?v">Sectors</a></li><br />              
              <li><a href="aggregator/data-event-schedule.php?v">Schedule</a></li><br />              
              <li><a href="aggregator/data-feedback.php?v">Feedback</a></li>              
              <li><a href="aggregator/data-uploads.php?v">Uploads</a></li>              
              <li><a href="aggregator/version.php">V</a></li>              
              <li><a id='logout' href="aggregator/index.php?logout">[logout]</a></li>              
            </ul>
          </div><!--/.nav-collapse -->
        <?php endif; ?>
        </div>
      </div>
    </div>
  <p>&nbsp;</p>
    <div class="xcrud container bodyContainer">      

      <?php if($login): ?>
            <h1>WorldSkills Mobile Aggregator</h1>
            <p>Debug page for the aggregator.</p>
      <?php else: ?>
        <h1>WorldSkills Mobile Aggregator</h1>
        <form action='index.php' method='POST'>
          <label>Password:</label>
          <input type='password' name='ag_login' id='ag_login' />
          <input type='submit' value='Login' />
        </form>
      <?php endif; ?>

      <?php if($login_error): ?>
        <h2 class='red'>Wrong login!</h2>
      <?php endif; ?>

      <?php       
        if($login && isset($_REQUEST['edit'])){

          include("assets/xcrud/xcrud.php");
          $xcrud = Xcrud::get_instance();          
          //$xcrud->jquery_no_conflict(true);
          //$xcrud->force_tinymce(true);

          switch($_REQUEST['edit']){
            case 'photos':          
              $xcrud->table_name('Flickr Highlight Photo Sets');    
              $xcrud->table('app_photo_highlights');
              $xcrud->columns('title, flickr_set_url, modified');
              $xcrud->fields('title, flickr_set_url, modified');
              $xcrud->no_editor('flickr_set_url');

              echo $xcrud->render();              
            break;
            case 'test_projects':
              $xcrud->table_name('Test Projects');    
              $xcrud->table('test_projects');
              $xcrud->columns('competition_trade_id, title_en, title_de, subtitle_en, subtitle_de, description_en, description_de, images, modified');
              $xcrud->fields('title_en, title_de, subtitle_en, subtitle_de, description_en, description_de, images, modified');
              $xcrud->where('competition_id = ', COMPETITION_ID);
              $xcrud->order_by('competition_trade_id');
              $xcrud->no_editor('images');

              echo $xcrud->render();
            break;
            case 'skill_highlights':
              $xcrud->table_name('Skill Highlight Pictures');    
              $xcrud->table('internal_trades');
              $xcrud->columns('name_en, highlight_images, modified');
              $xcrud->fields('highlight_images, modified');
              $xcrud->limit('50');
              $xcrud->where("", 'id IN (SELECT internal_trade_id FROM competition_trades WHERE competition_id = '. COMPETITION_ID . ')');
              $xcrud->order_by('name_en');
              //$xcrud->change_type('highlight_images', 'image', UPLOADED_IMAGES, array('width' => 608));//608(width:100%)x360px
              //$xcrud->change_type('highlight_images', 'image', UPLOADED_IMAGES, array('width' => 608, 'height' => 360, 'crop' => true));//608(width:100%)x360px
              $xcrud->no_editor('highlight_images');

              $xcrud->unset_add();
              $xcrud->unset_remove();

              echo $xcrud->render();            
            break;
            case 'skill_descriptions':
              $xcrud->table_name("Skill Definitions");
              $xcrud->table('app_skill_definitions');

              $xcrud->relation('internal_trade_id','internal_trades','id','name_en', 'id IN (SELECT internal_trade_id FROM competition_trades WHERE competition_id = '. COMPETITION_ID . ')');
              $xcrud->label('group_id','Category');

              $xcrud->columns('internal_trade_id, required_skills_en, required_skills_de, industry_action_en, industry_action_de, modified');
              $xcrud->fields('internal_trade_id, required_skills_en, required_skills_de, industry_action_en, industry_action_de, modified');
              $xcrud->limit('50');
              //$xcrud->where("", 'internal_trade_id IN (SELECT internal_trade_id FROM competition_trades WHERE competition_id = '. COMPETITION_ID . ')');
              $xcrud->order_by('internal_trade_id');

              //$xcrud->unset_add();
//              $xcrud->unset_remove();

              echo $xcrud->render();
            break;
            case 'event_schedule':
              $xcrud->table_name("Event Schedule");
              $xcrud->table('app_event_schedule');

              $xcrud->relation('skill','internal_trades','id','name_en', 'id IN (SELECT internal_trade_id FROM competition_trades WHERE competition_id = '. COMPETITION_ID . ')');

              $xcrud->columns('competition_day, datestamp, start_time, end_time, skill, title, description, modified');
              $xcrud->fields('competition_day, datestamp, start_time, end_time, skill, title, description, modified');
              $xcrud->limit('50');
              //$xcrud->where("", 'internal_trade_id IN (SELECT internal_trade_id FROM competition_trades WHERE competition_id = '. COMPETITION_ID . ')');
              $xcrud->order_by('datestamp');

              //$xcrud->unset_add();
//              $xcrud->unset_remove();

              echo $xcrud->render();
            break;            
            case 'events':
              $xcrud->table_name('Event Information');
              $xcrud->table('app_events');
              $xcrud->columns('title_en, title_de, content_en, content_de, content_image, modified');
              $xcrud->fields('title_en, title_de, content_en, content_de, content_image, modified');
              $xcrud->order_by('id');
              $xcrud->change_type('content_image', 'image', UPLOADED_IMAGES, array('width' => 608, 'height' => 360, 'crop' => true));//608(width:100%)x360px

              echo $xcrud->render();
            break;
            case 'venues':
              $xcrud->table_name('Venue Information');
              $xcrud->table('app_venues');
              $xcrud->columns('title_en, title_de, content_en, content_de, content_image, modified');
              $xcrud->fields('title_en, title_de, content_en, content_de, content_image, modified');
              $xcrud->order_by('id');
              $xcrud->change_type('content_image', 'image', UPLOADED_IMAGES, array('width' => 608, 'height' => 360, 'crop' => true));//608(width:100%)x360px

              echo $xcrud->render();            
            break;  
            case 'sectors':
              $xcrud->table_name('Sector Information');
              $xcrud->table('competition_trade_sectors');
              $xcrud->columns('sector_name, sector_name_de, sector_description, sector_description_de, sector_order, sector_baseColor, sector_secondaryColor, sector_labelColor');
              $xcrud->fields('sector_name, sector_name_de, sector_description, sector_description_de, sector_order, sector_baseColor, sector_secondaryColor, sector_labelColor');
              $xcrud->order_by('sector_order');
              $xcrud->where('id >', 0);

              echo $xcrud->render();     

            break;          
            case 'sponsors':            
              $xcrud->table_name('Skill Sponsor Information');
              $xcrud->table('app_sponsors');

              $xcrud->relation('group_id','app_sponsor_groups','id','title_en');
              $xcrud->label('group_id','Category');

              $xcrud->columns('group_id, name, logo_thumb_url, profile_image_url, profile_title_en, profile_title_de, profile_description_en, profile_description_de, profile_video_url, read_more_url, modified');
              $xcrud->fields('group_id, name, logo_thumb_url, profile_image_url, profile_title_en, profile_title_de, profile_description_en, profile_description_de, profile_video_url, read_more_url, modified');
              $xcrud->order_by('group_id, name');

              $xcrud->change_type('logo_thumb_url', 'image', UPLOADED_IMAGES, array('width' => 200, 'height' => 175));
              $xcrud->change_type('profile_image_url', 'image', UPLOADED_IMAGES, array('width' => 608, 'height' => 360, 'crop' => true));//608(width:100%)x360px

              echo $xcrud->render();     
            break;
            case 'skill_sponsors':
              $xcrud->table_name('Sponsor Information');
              $xcrud->table('skill_sponsors');

              $xcrud->relation('competition_id','competitions','id','name');
              $xcrud->label('competition_id','Competition');

              $xcrud->columns('competition_id, competition_trade_id, name, logo_thumb_url, profile_image_url, profile_title_en, profile_title_de, profile_description_en, profile_description_de, profile_video_url, read_more_url, modified');
              $xcrud->fields('competition_id, competition_trade_id, name, logo_thumb_url, profile_image_url, profile_title_en, profile_title_de, profile_description_en, profile_description_de, profile_video_url, read_more_url, modified');
              $xcrud->order_by('competition_trade_id, name');

              $xcrud->change_type('logo_thumb_url', 'image', UPLOADED_IMAGES, array('width' => 200, 'height' => 175));
              $xcrud->change_type('profile_image_url', 'image', UPLOADED_IMAGES, array('width' => 608, 'height' => 360, 'crop' => true));//608(width:100%)x360px

              echo $xcrud->render();     
            break;    
            case 'local-push':
              $xcrud->table_name('Local Push Notifications (GLOBAL)');
              $xcrud->table('app_local_push_global');

              $xcrud->columns('timestamp, text_en, text_de, modified');
              $xcrud->fields('timestamp, text_en, text_de, modified');

              $xcrud->order_by('timestamp');          

              echo $xcrud->render();             
            break;        
            case 'sponsor_groups':
              $xcrud->table_name('Sponsor Groups');
              $xcrud->table('app_sponsor_groups');
              $xcrud->columns('title_en, ordernum, modified');
              $xcrud->fields('title_en, ordernum, modified');
              $xcrud->order_by('ordernum');

              echo $xcrud->render();     
            break;
            default:
              echo "nothing to edit...";
              break;
          }

        }//
        else if($login && isset($_REQUEST['force'])){
          if(strstr($_REQUEST['force'], "?"))
            $force_url = "aggregator/" . $_REQUEST['force'] . "&v=1&f=1";
          else
            $force_url = "aggregator/" . $_REQUEST['force'] . "?v=1&f=1";

          echo "<script type='text/javascript'>";
          echo "$('.bodyContainer').fadeOut(500, function(){ ";
          echo "  $('.bodyContainer').html('Loading, please wait...');";
          echo "  $(this).fadeIn(500, function(){";
          echo "$('.bodyContainer').load('".$force_url."');";
          echo "  });";
          echo "});";
          echo "</script>";
        }
      ?>

    </div> <!-- /container -->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="assets/js/bootstrap-transition.js"></script>
    <script src="assets/js/bootstrap-alert.js"></script>
    <script src="assets/js/bootstrap-modal.js"></script>
    <script src="assets/js/bootstrap-dropdown.js"></script>
    <script src="assets/js/bootstrap-scrollspy.js"></script>
    <script src="assets/js/bootstrap-tab.js"></script>
    <script src="assets/js/bootstrap-tooltip.js"></script>
    <script src="assets/js/bootstrap-popover.js"></script>
    <script src="assets/js/bootstrap-button.js"></script>
    <script src="assets/js/bootstrap-collapse.js"></script>
    <script src="assets/js/bootstrap-carousel.js"></script>
    <script src="assets/js/bootstrap-typeahead.js"></script>

  </body>
</html>
