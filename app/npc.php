<?php
declare(strict_types=1);

$cloaked[] = 'opponents/blood_amoeba.png';
$cloaked[] = 'opponents/ceylacennia.png';
$cloaked[] = 'opponents/cyborg_manta.png';
$cloaked[] = 'opponents/manifestation_developed.png';
$cloaked[] = 'opponents/dreadscorps.png';
$cloaked[] = 'opponents/drosera.png';
$cloaked[] = 'opponents/energy_minnow.png';
$cloaked[] = 'opponents/energy_sparker.png';
$cloaked[] = 'opponents/smuggler_escorted.png';
$cloaked[] = 'opponents/pirate_experienced.png';
$cloaked[] = 'opponents/pirate_famous.png';
$cloaked[] = 'opponents/frost_crystal.png';
$cloaked[] = 'opponents/gorefanglings.png';
$cloaked[] = 'opponents/gorefangling.png';
$cloaked[] = 'opponents/gorefang.png';
$cloaked[] = 'opponents/hidden_drug_stash.png';
$cloaked[] = 'opponents/pirate_inexperienced.png';
$cloaked[] = 'opponents/infected_creature.png';
$cloaked[] = 'opponents/smuggler_lone.png';
$cloaked[] = 'opponents/lucidi_squad.png';
$cloaked[] = 'opponents/nebula_mole.png';
$cloaked[] = 'opponents/nebula_serpent.png';
$cloaked[] = 'opponents/oblivion_vortex.png';
$cloaked[] = 'opponents/manifestation_ripe';
$cloaked[] = 'opponents/sarracenia.png';
$cloaked[] = 'opponents/slave_trader.png';
$cloaked[] = 'opponents/manifestation_verdant.png';
$cloaked[] = 'opponents/locust_hive.png';
$cloaked[] = 'opponents/vyrex_hatcher.png';
$cloaked[] = 'opponents/vyrex_assassin.png';
$cloaked[] = 'opponents/vyrex_stinger.png';
$cloaked[] = 'opponents/vyrex_mutant_mauler.png';
$cloaked[] = 'opponents/vyrex_larva.png';
$cloaked[] = 'opponents/pirate_captain.png';
$cloaked[] = 'opponents/starclaw.png';

sort($cloaked);

$single[] = 'opponents/shadow.png';
$single[] = 'opponents/feral_serpent.png';

sort($single);

$hack[] = 'opponents/shadow.png';
$hack[] = 'opponents/pirate_experienced.png';
$hack[] = 'opponents/energybees.png';

sort($hack);

$nonblocking[] = 'opponents/slave_trader.png';
$nonblocking[] = 'opponents/smuggler_lone.png';
$nonblocking[] = 'opponents/smuggler_escorted.png';
//$nonblocking[] = 'foreground/wormhole.png';
$nonblocking[] = 'opponents/gorefanglings.png';
$nonblocking[] = 'opponents/gorefang.png';
$nonblocking[] = 'opponents/nebula_mole.png';
$nonblocking[] = 'opponents/hidden_drug_stash.png';
$nonblocking[] = 'opponents/space_clam.png';
$nonblocking[] = 'opponents/preywinder.png';
$nonblocking[] = 'opponents/glowprawn.png';
$nonblocking[] = 'opponents/starclaw.png';
$nonblocking[] = 'opponents/eulerian.png';
$nonblocking[] = 'opponents/vyrex_hatcher.png';
$nonblocking[] = 'opponents/vyrex_assassin.png';
$nonblocking[] = 'opponents/vyrex_stinger.png';
$nonblocking[] = 'opponents/vyrex_mutant_mauler.png';
$nonblocking[] = 'opponents/vyrex_larva.png';

sort($nonblocking);

// These NPC move around but may have limited count, need to not show cloaked location if another is spotted within the same range(new query needed to find ranged matches) NOTED
$mobile[] = 'opponents/space_dragon_queen.png';
$mobile[] = 'opponents/cyborg_manta.png';
$mobile[] = 'opponents/lucidi_squad.png';
$mobile[] = 'opponents/pirate_famous.png';
$mobile[] = 'opponents/pirate_captain.png';
$mobile[] = 'opponents/preywinder.png';
$mobile[] = 'opponents/vyrex_mutant_mauler.png';

sort($mobile);
