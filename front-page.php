<?php
/**
 * * The template for displaying the front page
 *
 * @package your-wp-project
 */

get_header();?>
	<main id="main-content">
		<section class="about page-section" id="about" aria-labelledby="about-heading" data-matching-link="#about-link">
			<section class="about__header" aria-labelledby="about-heading">
				<h2 class="h2__heading" id="about-heading">About</h2>
				<p class="body-text">Project Roadmap delivers Training and Technical Assistance (TTA) exclusively to <b>OVC Enhanced Collaborative Model (ECM) grantees and their partners</b>, designed to meet the needs of individual grantees and ECM task forces as multi-disciplinary teams.</p>
			</section>
			<section class="about__header page-section" id="resources-link" aria-labelledby="home-resources-heading" data-matching-link="#resources-nav-link">
				<h2 class="h2__heading" id="home-resources-heading">Resources</h2>
				<p class="body-text">Tools, guides, and templates for ECM task forces. Search, filter by type, or browse the full library below.</p>
				<a class="body-cta" href="<?php echo esc_url( projectroadmap_resources_url() ); ?>">
					<span>Explore Resources</span>
					<?php svg_icon( 'body-cta__arrow', 'angle-right' ); ?>
				</a>
			</section>

			<section class="strategy page-section" id="strategy" aria-labelledby="strategy-heading" data-matching-link="#strategy-link">
				<header class="strategy__header">
					<h2 class="strategy__heading h2__heading" id="strategy-heading">Our Strategy</h2>
					<p class="strategy__description body-text">We take a dynamic, three-level approach to support the needs of ECM task forces.</p>
				</header>

				<div class="about__wrapper">
				<article class="about__item" id="task-force">
					<div class="about__gradient">
						<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/img/backgrounds/task-force.jpg' ); ?>" alt="Investigators reviewing case information together" loading="lazy">
					</div>
					<div class="about__details">
						<h3 class="h3__heading">Task Force</h3>
						<p class="about__p body-text">Each ECM Task Force is offered individual mentoring through Task Force Liaisons (TFLs). This technical assistance (TA) includes support in key areas such as: structure, investigations, data-driven approaches, and training/awareness.</p>
					</div>
				</article>
				<article class="about__item" id="discipline">
					<div class="about__gradient">
						<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/img/backgrounds/discipline.jpg' ); ?>" alt="Presenter leading a professional workshop" loading="lazy">
					</div>
					<div class="about__details">
						<h3 class="h3__heading">Discipline</h3>
						<p class="about__p body-text">Our field coaching workshops create space for each ECM discipline to collaborate and learn from each other furthering the expertise of coordinators, law enforcement, service providers, and prosecutors.&lrm;</p>
					</div>
				</article>
				<article class="about__item" id="field-at-large">
					<div class="about__gradient">
						<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/img/backgrounds/field-at-large.jpg' ); ?>" alt="Workshop participants raising their hands" loading="lazy">
					</div>
					<div class="about__details">
						<h3 class="h3__heading">Field At-Large</h3>
						<p class="about__p body-text">Professionals from across the country convene in a collaborative effort to identify and explore gaps in processes and training. Once assessed, these issues are addressed through training solutions for multidisciplinary task force teams.</p>
					</div>
				</article>
				</div>
			</section>
		</section>
		<section class="staff page-section" id="team" aria-labelledby="team-heading" data-matching-link="#team-link">
			<div class="staff__wrapper">
				<header class="staff__header">
					<h2 class="h2__heading" id="team-heading">The team that supports teams.</h2>
					<p  class="body-text">The Project Roadmap team has extensive experience in the field, making them well-equipped in
						providing TTA to help task forces reach their goals. Their expertise has been developed firsthand through years of experience in; directing human trafficking task forces, providing direct services, and investigating and prosecuting human trafficking.</p>
				</header>
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
									<img class="staff__headshot" src="<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'staff-headshot' ) ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" loading="lazy">
									<div class="staff__details">
										<p class="staff__name"><?php the_title();?></p>
									</div>
								</div>
								<!-- .Individual staff -->
							<?php }
								} else { ?>
								<p class="staff__no-show">There is no staff to show yet</p>
						<?php }
						wp_reset_postdata(); ?>
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
								<img class="staff__headshot" src="<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'staff-headshot' ) ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" loading="lazy">
									<div class="staff__details">
										<p class="staff__name"><?php the_title();?></p>
									</div>
								</div>
								<!-- .Individual staff -->
						<?php }
							} else { ?>
							<p class="staff__no-show">There is no task force liaisons to show yet</p>
					<?php }
					wp_reset_postdata(); ?>
				</div>
				<section class="questions page-section" id="contact" aria-labelledby="contact-heading" data-matching-link="#contact-link">
					<h2 class="h3__heading" id="contact-heading">Have questions? Need more info?</h2>
					<p>Contact us at: <a class="questions__email" href="mailto:projectroadmap@icf.com">projectroadmap@icf.com</a></p>
				</section>
			</div>
		</section>
	</main>
<?php get_footer(); ?>
