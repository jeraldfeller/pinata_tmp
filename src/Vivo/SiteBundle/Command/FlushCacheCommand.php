<?php

namespace Vivo\SiteBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FlushCacheCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setDescription('Flush opcache and varnish.')
            ->setName('vivosite:cache:flush')
            ->addOption(
                'basic',
                null,
                InputOption::VALUE_NONE,
                'If set, only a 1 or 0 result will be outputted.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var \Vivo\SiteBundle\Repository\SiteRepository $siteRepository */
        $siteRepository = $this->getContainer()->get('vivo_site.repository.site');
        $site = $siteRepository->findPrimarySite();

        if (!$site) {
            return;
        }

        /** @var \Symfony\Component\Routing\RouterInterface $router */
        $router = $this->getContainer()->get('router');

        $timestamp = new \DateTime('now', new \DateTimeZone('UTC'));

        $url = $site->getPrimaryDomain()->getUrl().$router->generate('vivo_site.site.flush', array(
            'random' => substr(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36), 0, 16),
            'timestamp' => $timestamp->getTimestamp(),
        ));

        /** @var \Symfony\Component\HttpKernel\UriSigner $uriSigner */
        $uriSigner = $this->getContainer()->get('uri_signer');
        $requestUrl = $uriSigner->sign($url);

        if (!$input->getOption('basic')) {
            $output->writeln(sprintf('Requesting <comment>%s</comment>', $requestUrl));
        }

        try {
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => $requestUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FAILONERROR => false,
                CURLOPT_HEADER => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_FOLLOWLOCATION => true,
            ));

            $result = curl_exec($ch);

            if (curl_errno($ch)) {
                curl_close($ch);

                if ($input->getOption('basic')) {
                    $output->write('0');
                } else {
                    $output->writeln('<error>Request failed.</error>');
                }

                return;
            }

            curl_close($ch);

            if ('1' === $result) {
                if ($input->getOption('basic')) {
                    $output->write('1');
                } else {
                    $output->writeln('<info>Request succeeded.</info>');
                }

                return;
            }
        } catch (\Exception $e) {
        }

        if ($input->getOption('basic')) {
            $output->write('0');
        } else {
            $output->writeln(sprintf('<error>Request failed: </error> %s', $requestUrl));
        }
    }
}
