<?php

namespace App\Command;

use App\Repository\NewsRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class SendTopTenNewsCommand extends Command
{
    private NewsRepository $newsRepository;
    private MailerInterface $mailer;
    private Environment $twig;

    public function __construct(NewsRepository $newsRepository, MailerInterface $mailer, Environment $twig)
    {
        parent::__construct('app:send-top-ten-news');
        $this->newsRepository = $newsRepository;
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    protected function configure(): void
    {
        $this->setDescription('Sends the Top 10 news statistics to the designated email address.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $top10News = $this->newsRepository->findTopTenNews();

        $emailContent = $this->twig->render('emails/top_ten_news.html.twig', [
            'newsList' => $top10News,
        ]);

        $email = (new Email())
            ->from('no-reply@example.com')
            ->to('designated@example.com')
            ->subject('Weekly Top 10 News Statistics')
            ->html($emailContent);

        $this->mailer->send($email);

        $output->writeln('Top 10 news email sent successfully.');

        return Command::SUCCESS;
    }
}