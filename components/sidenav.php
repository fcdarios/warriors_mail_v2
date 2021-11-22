
<div class="nav">
      <div class="sb-sidenav-menu-heading"><?php  echo __('administrations03');?></div>
      <a class="nav-link nav-links-wr  <?php  echo  $link_activo == 'dashboard' ? 'active' : ''; ?>" href="dashboard.php">
         <div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div>
         <?php echo __('dashboard'); ?>
      </a>
      <a class="nav-link nav-links-wr <?php  echo  $link_activo == 'status' ? 'active' : ''; ?>" href="status.php">
         <div class="sb-nav-link-icon"><i class="fas fa-inbox"></i></div>
         <?php echo __('recentmessages03'); ?>
      </a>
      <a class="nav-link nav-links-wr  <?php  echo  $link_activo == 'lists' ? 'active' : ''; ?>" href="lists.php">
         <div class="sb-nav-link-icon"><i class="far fa-list-alt"></i></div>
         <?php echo __('lists03'); ?>
      </a>
     
      <a class="nav-link nav-links-wr <?php  echo  $link_activo == 'reports' ? 'active' : ''; ?>" href="reports.php">
         <div class="sb-nav-link-icon"><i class="fas fa-folder-open"></i></div>
         <?php echo __('reports03'); ?>
      </a>
      <?php if ($_SESSION['user_type'] === 'A'): ?>
      <a class="nav-link nav-links-wr <?php  echo  $link_activo == 'MCP' ? 'active' : ''; ?>" href="mcp.php">
         <div class="sb-nav-link-icon"><i class="fas fa-external-link-square-alt"></i></div>
         <?php echo __('mcp03'); ?>
      </a>
      <a class="nav-link nav-links-wr <?php  echo  $link_activo == 'services' ? 'active' : ''; ?>" href="services.php">
         <div class="sb-nav-link-icon"><i class="fas fa-server"></i></div>
         <?php echo __('services03'); ?>
      </a>
      <?php endif; ?>
      
      <a class="nav-link nav-links-wr  <?php  echo  $link_activo == 'quarantine' ? 'active' : ''; ?>" href="quarantine.php">
         <div class="sb-nav-link-icon"><i class="fas fa-ban"></i></div>
         <?php echo __('quarantine03'); ?>
      </a>

      <a class="nav-link nav-links-wr  <?php  echo  $link_activo == 'other' ? 'active' : ''; ?>" href="other.php">
         <div class="sb-nav-link-icon"><i class="fas fa-external-link-alt"></i></div>
         <?php echo __('toolslinks03'); ?>
      </a>
      
      <a class="nav-link collapsed nav-links-wr  <?php  echo  $link_activo == 'grey' ? 'active' : ''; ?>" href="#" data-toggle="collapse" data-target="#collapseLayouts_greylist" aria-expanded="false" aria-controls="collapseLayouts_greylist">
         <div class="sb-nav-link-icon"><i class="fas fa-list-alt"></i></div>
         <?php echo __('greylist65'); ?>
         <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
      </a>

      <div class="collapse" id="collapseLayouts_greylist" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
         <nav class="sb-sidenav-menu-nested nav">
           
            <a class="nav-link nav-links-wr <?php  echo  $greylink == 'sgwi_connect' ? 'active' : ''; ?>" 
            href="sgwi_connect.php" title="hosts/domains that are currently greylisted">
               <i class="fas fa-caret-right mr-1"></i>
               <?php echo __('greywaiting65'); ?>
            </a>
            <a class="nav-link nav-links-wr <?php  echo  $greylink == 'sgwi_email' ? 'active' : ''; ?>"
            href="sgwi_awl.php?mode=email" 
				title="auto-whitelisted e-mailadresses (that have passed greylisting)">
               <i class="fas fa-caret-right mr-1"></i>
               <?php echo __('greyemailaddr65'); ?>
            </a>
            <a class="nav-link nav-links-wr <?php  echo  $greylink == 'sgwi_domain' ? 'active' : ''; ?>" 
            href="sgwi_awl.php?mode=domains" 
				title="auto-whitelisted domains (that have passed greylisting)">
               <i class="fas fa-caret-right mr-1"></i>
               <?php echo __('domain44'); ?>
            </a>
            
            <a class="nav-link nav-links-wr <?php  echo  $greylink == 'sgwi_opt_out_domain' ? 'active' : ''; ?>" 
            href="sgwi_opt_in_out.php?direction=out&amp;what=domain">
               <i class="fas fa-caret-right mr-1"></i>
               <?php echo __('greyoutdomain65'); ?>
            </a>
            <a class="nav-link nav-links-wr <?php  echo  $greylink == 'sgwi_opt_out_email' ? 'active' : ''; ?>" 
            href="sgwi_opt_in_out.php?direction=out&amp;what=email">
               <i class="fas fa-caret-right mr-1"></i>
               <?php echo __('greyoutemail65'); ?>
            </a>

            <a class="nav-link nav-links-wr <?php  echo  $greylink == 'sgwi_opt_in_domain' ? 'active' : ''; ?>" 
            href="sgwi_opt_in_out.php?direction=in&amp;what=domain">
               <i class="fas fa-caret-right mr-1"></i>
               <?php echo __('greyindomain65'); ?>
            </a>
            <a class="nav-link nav-links-wr <?php  echo  $greylink == 'sgwi_opt_in_email' ? 'active' : ''; ?>" 
            href="sgwi_opt_in_out.php?direction=in&amp;what=email">
               <i class="fas fa-caret-right mr-1"></i>
               <?php echo __('greyinemail65'); ?>
            </a>
         </nav>
      </div>
      
</div>
         