<?php
namespace ADPDH\Theme;
defined('ABSPATH') || exit;
$c=fn($key,$default)=>e($copy[$key]??$default);
$feedback=null;$token=request('feedback');
if (preg_match('/^[a-f0-9]{32}$/D',$token)) $feedback=get_transient('adpdh_feedback_'.$token);
?>
<div class="container breadcrumb" aria-label="Fil d’Ariane"><a href="<?php echo e(route('home')); ?>">Accueil</a><span aria-hidden="true">/</span><span>Contact</span></div>
<section class="container about-heading"><p class="eyebrow"><?php echo $c('eyebrow','Contact'); ?></p><h1><?php echo $c('heading','Un lien direct'); ?><br><em><?php echo $c('heading_accent','avec ADPDH.'); ?></em></h1><p><?php echo $c('introduction','Une question, une information ou un premier échange : écrivez à notre équipe.'); ?></p></section>
<section class="container contact-layout"><aside class="contact-details"><h2><?php echo $c('address_title','Nous retrouver'); ?></h2>
<?php foreach (['kinshasa','bukavu','uvira'] as $city): if (!empty($settings['adpdh.address.'.$city])): ?><address><?php echo nl2br(e($settings['adpdh.address.'.$city])); ?></address><?php endif; endforeach; ?>
<h3><?php echo $c('email_title','Nous écrire'); ?></h3><a href="mailto:<?php echo e($settings['adpdh.contact.email']??''); ?>"><?php echo e($settings['adpdh.contact.email']??''); ?> ↗</a>
<?php if (!empty($settings['adpdh.contact.phone'])): ?><h3><?php echo $c('phone_title','Nous appeler'); ?></h3><a href="tel:<?php echo e(preg_replace('/[^+0-9]/','',$settings['adpdh.contact.phone'])); ?>"><?php echo e($settings['adpdh.contact.phone']); ?></a><?php endif; ?>
<div class="contact-purpose"><h3><?php echo $c('partnership_title','Un projet de collaboration ?'); ?></h3><p><?php echo $c('partnership_text','Découvrez les différentes formes de soutien.'); ?></p><a class="text-link" href="<?php echo e(route('partnership')); ?>"><?php echo $c('partnership_link','Devenir partenaire →'); ?></a></div></aside>
<div class="contact-form-panel"><h2><?php echo $c('form_title','Votre message'); ?></h2><p id="form-notice"><?php echo e(str_replace('{email}',$settings['adpdh.contact.email']??'', $copy['form_notice']??'Votre message sera envoyé à {email}. Les champs ci-dessous sont obligatoires.')); ?></p>
<?php if (!empty($feedback['success'])): ?><p class="form-notice" role="status">Votre message a été transmis à notre service de messagerie. Merci de nous avoir contactés.</p><?php endif; ?>
<?php if (!empty($feedback['errors'])): ?><div class="form-notice" role="alert"><p>Le message n’a pas été envoyé.</p><ul><?php foreach ($feedback['errors'] as $error): ?><li><?php echo e($error); ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post" action="<?php echo e(admin_url('admin-post.php')); ?>" aria-describedby="form-notice">
<?php wp_nonce_field('adpdh_contact','adpdh_contact_nonce'); ?><input type="hidden" name="action" value="adpdh_contact"><div class="adpdh-honeypot" hidden aria-hidden="true"><label>Site web<input name="website" tabindex="-1" autocomplete="off"></label></div>
<div class="form-pair"><div><label for="contact-name"><?php echo $c('name_label','Nom'); ?></label><input id="contact-name" name="name" type="text" autocomplete="name" required minlength="2" maxlength="120"></div><div><label for="contact-email"><?php echo $c('email_label','E-mail'); ?></label><input id="contact-email" name="email" type="email" autocomplete="email" required maxlength="254"></div></div>
<label for="contact-subject"><?php echo $c('subject_label','Sujet'); ?></label><input id="contact-subject" name="subject" type="text" required minlength="3" maxlength="180"><label for="contact-message"><?php echo $c('message_label','Message'); ?></label><textarea id="contact-message" name="message" rows="7" required minlength="10" maxlength="5000"></textarea>
<p class="source-note"><?php echo $c('privacy_text','Les informations saisies sont transmises à l’équipe ADPDH pour répondre à votre demande.'); ?></p><a class="text-link form-privacy" href="<?php echo e(home_url('/mentions-legales/#confidentialite')); ?>"><?php echo $c('privacy_link','Consulter les informations de confidentialité'); ?></a><div><button type="submit" class="button"><?php echo $c('submit_label','Envoyer le message →'); ?></button></div>
</form></div></section>
