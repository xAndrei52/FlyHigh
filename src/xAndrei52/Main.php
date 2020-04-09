<?php


namespace xAndrei52\Main;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as C;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener
{

    private $config;

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        $this->saveResource('Config.yml');
        $this->config = new Config($this->getDataFolder() . 'Config.yml', Config::YAML);
    }

    public function onPlayerJoin(PlayerJoinEvent $event)
    {
        $player = $event->getPlayer();

        if($player->getAllowFlight()) {
            $player->setFlying(false);
            $player->setAllowFlight(false);
            $player->sendMessage(C::AQUA . "[!] Your fly was deactivated.");
        }
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        if($command->getName() === "fliegen"){
            if(!$sender instanceof Player){
                $sender->sendMessage("Please use this comamnd in-game");
                return false;
            }

            if(isset($args[0])){
                if(!$sender->hasPermission("simplefly.command.other")){
                    $sender->sendMessage(C::RED . "[!] You need to specify a player that you wanna deactivate his fly.");
                    return false;
                }
                $target = $sender->getServer()->getPlayer($args[0]);
                if(!$target instanceof Player){
                    $sender->sendMessage(C::RED . "[!] I cannot find this player! maybe he is offline?");
                    return false;
                }
                if($target->getAllowFlight()){
                    $target->setFlying(false);
                    $target->setAllowFlight(false);
                    $target->sendMessage(C::YELLOW . "[!] Your fly was deactivated by an OP-Player.");
                    $sender->sendMessage(C::GREEN . "[!] Fly for" . $target->getName() . "'Was deactivated sucesfully.");
                } else {
                    $target->setAllowFlight(true);
                    $target->setFlying(true);
                    $target->sendMessage(C::GREEN . "[!] Your fly mode was activated by an OP-Player.");
                    $sender->sendMessage(C::GREEN . "[!] Fly for" . $target->getName() . "Was activated sucesfully.");
                }
                return false;
            }

            if($sender->getAllowFlight()){
                $sender->setFlying(false);
                $sender->setAllowFlight(false);
                $sender->sendMessage(C::GREEN . "[!] Your fly mode was deactivated sucesfully!");
            } else {
                $sender->setAllowFlight(true);
                $sender->setFlying(true);
                $sender->sendMessage(C::GREEN . "[!] Your fly mode was activated sucesfully!");
            }
        }
        return false;
    }

    public function onEntityDamageEntity(EntityDamageByEntityEvent $event) : void
    {
        $entity = $event->getEntity();
        $damager = $event->getDamager();

        if($event->isCancelled() || $this->getConfig()->get("flydisable-onCombat") === true){
            return;
        }
        if(!$entity instanceof Player || !$damager instanceof Player){
            return;
        }

        if($entity->getAllowFlight()){
            $entity->setFlying(false);
            $entity->setAllowFlight(false);
            $entity->sendMessage(C::RED . "[!] Your flying has been disabled");
        }
        if($damager->getAllowFlight()){
            $damager->setFlying(false);
            $damager->setAllowFlight(false);
            $damager->sendMessage(C::RED . "[!] Your flying was been disabled.");
        }
    }


}
