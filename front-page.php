<?php
/**
 * * The template for displaying the front page
 *
 * @package your-wp-project
 */

get_header();?>
	<main id="main-content">
        <section class="about page-section" id="about" data-matching-link="#about-link">
					<div class="about__header">
						<h2 class="h2__heading">About</h2>
						<p class="body-text">Project Roadmap delivers Training and Technical Assistance (TTA) exclusively to <b>OVC Enhanced Collaborative Model (ECM) grantees and their partners</b>, designed to meet the needs of individual grantees and ECM task forces as multi-disciplinary teams.</p>
						<h3 class="h3__heading">Our Strategy</h3>
						<p class="body-text">We take a dynamic, three-level approach to support the needs of ECM task forces.</p>
					</div>
					<div class="about__wrapper">
						<div class="about__item" id="task-force">
							<div class="about__gradient">
								<img src="<?php echo get_stylesheet_directory_uri()?>/assets/img/backgrounds/task-force.jpg" alt="">
							</div>
							<div class="about__details">
								<h3 class="h3__heading">Task Force</h3>
								<p class="about__p body-text">Each ECM Task Force is offered individual mentoring through Task Force Liaisons (TFLs). This technical assistance (TA) includes support in key areas such as: structure, investigations, data-driven approaches, and training/awareness.</p>
							</div>
						</div>
						<div class="about__item" id="discipline">
						<div class="about__gradient">
							<img src="<?php echo get_stylesheet_directory_uri()?>/assets/img/backgrounds/discipline.jpg" alt="">
						</div>
							<div class="about__details">
								<h3 class="h3__heading">Discipline</h3>
								<p class="about__p body-text">Our field coaching workshops create space for each ECM discipline to collaborate and learn from each other furthering the expertise of coordinators, law enforcement, service providers, and prosecutors.&lrm;</p>
							</div>
						</div>
						<div class="about__item" id="field-at-large">
							<div class="about__gradient">
								<img src="<?php echo get_stylesheet_directory_uri()?>/assets/img/backgrounds/field-at-large.jpg" alt="">
							</div>
							<div class="about__details">
								<h3 class="h3__heading">Field At-Large</h3>
								<p class="about__p body-text">Professionals from across the country convene in a collaborative effort to identify and explore gaps in processes and training. Once assessed, these issues are addressed through training solutions for multidisciplinary task force teams.</p>
							</div>
						</div>
					</div>
				</section>
				<section class="staff page-section" id="team" data-matching-link="#team-link">
					<div class="staff__wrapper">
						<div class="staff__header">
							<h2 class="h2__heading">The team that supports teams.</h2>
							<p  class="body-text">The Project Roadmap team has extensive experience in the field, making them well-equipped in
								providing TTA to help task forces reach their goals. Their expertise has been developed firsthand through years of experience in; directing human trafficking task forces, providing direct services, and investigating and prosecuting human trafficking.</p>
						</div>
						<h3 class="h3__heading">Project Roadmap Staff</h3>
						<!-- Staff Wrapper -->
						<div class="staff__content">
						<?php
						$staff = new WP_Query(array(
							'posts_per_page' => -1,
							'post_type' => 'staff',
							'category_name' => 'project-roadmap-staff',
							'orderby'  => 'title',
							'order' => 'ASC'
							));
							if($staff->have_posts()) {
								while($staff->have_posts()) {
									$staff->the_post();?>
									<!-- Individual staff -->
									<?php $slug = get_post_field( 'post_name', get_the_ID() ); ?>
									<div class="staff__member<?php if ( wp_is_mobile() ){echo ' staff__is-mobile';}else{echo ' staff__is-desktop';}?>" >
										<span class="staff__overlay"></span>
										<img class="staff__headshot" src="<?php the_post_thumbnail_url('staff-headshot'); ?>" alt="<?php the_title();?>">
										<div class="staff__details">
											<p class="staff__name"><?php the_title();?></p>
										</div>
									</div>
									<!-- .Individual staff -->
								<?php }
									} else { ?>
									<p class="staff__no-show">There is no staff to show yet</p>
								<?php }?>
						</div> <!-- .Staff Wrapper -->
						<h3 class="h3__heading">Task Force Liaisons</h3>
						<div class="staff__content">
						<?php
						$staff = new WP_Query(array(
							'posts_per_page' => -1,
							'post_type' => 'staff',
							'category_name' => 'task-force-liaisons',
							'orderby'  => 'title',
							'order' => 'ASC'
							));
							if($staff->have_posts()) {
								while($staff->have_posts()) {
									$staff->the_post();?>
										<!-- Individual staff -->
										<?php $slug = get_post_field( 'post_name', get_the_ID() ); ?>
									<div class="staff__member<?php if ( wp_is_mobile() ){echo ' staff__is-mobile';}else{echo ' staff__is-desktop';}?>">
										<span class="staff__overlay"></span>
										<img class="staff__headshot" src="<?php the_post_thumbnail_url('staff-headshot'); ?>" alt="<?php the_title();?>">
										<div class="staff__details">
											<p class="staff__name"><?php the_title();?></p>
										</div>
									</div>
									<!-- .Individual staff -->
								<?php }
									} else { ?>
									<p class="staff__no-show">There is no task force liaisons to show yet</p>
								<?php }?>
						</div>
					</div>
				</section>
				<section class="resources page-section" id="resources" data-matching-link="#resources-link">
					<h2 class="h2__heading">Resources</h2>
					<div class="resources__wrapper">
            <?php
            // Display Resources
            $resources = new WP_Query(array(
							'post_type'=>'post',
							'post_status'=>'publish',
							'orderby'=> 'title',
							'order' => 'ASC',
							'posts_per_page'=>-1));
							if( $resources->have_posts() ):
									while( $resources->have_posts() ): $resources->the_post(); 

											// Get the first category of the post
											$categories = get_the_category();
											$category_name = !empty($categories) ? esc_html($categories[0]->name) : 'No Category'; // Fallback if no category

														$pdfcp = get_field('download_pdf');?>
											<a class="resource" href="<?php echo $pdfcp['url']; ?>" title="Click here to download the <?php echo the_title();?> Quick Guide" target="_blank" download>
													
												<div class="resource__download">
													<?php echo svg_icon('resource__icon', 'download');?>
												</div>
												<h4 class="h4__header"><?php echo $category_name; ?></h4>
												<p class="resource__name"  ><?php echo the_title(); ?></p>
											</a> 
									<?php endwhile;
							else : ?> <p class="p__body">No featured resources at this time</p>
									<?php
							wp_reset_postdata();
							endif;
            ?>
					</div>
					<div class="questions page-section" id="contact" data-matching-link="#contact-link">
						<h3 class="h3__heading">Have questions? Need more info?</h3>
						<p>Contact us at: <a class="questions__email" href="mailto:projectroadmap@icf.com" target="_blank">projectroadmap@icf.com</a></p>
					</div>
				</section>
	</main>
<?php get_footer(); ?>