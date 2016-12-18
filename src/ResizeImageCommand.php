<?php namespace Spline;

use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Intervention\Image\ImageManagerStatic as Image;

class ResizeImageCommand extends Command
{
    const DEFAULT_WIDTH = 860;

    public function configure()
    {
        $this->setName('resizeImage')
            ->setDescription('Resize image by width ratio.')
            ->addArgument('image', InputArgument::REQUIRED, 'The image file to be resized.')
            ->addOption('width', null, InputOption::VALUE_OPTIONAL, 'Resize to current width.');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        if (! class_exists('Intervention\Image\ImageManagerStatic')) {
            throw new RuntimeException('The InterventionImage is not installed. Please install it and try again.');
        }

        $imgFile = getcwd().'/'.$input->getArgument('image');
        $this->verifyImage($imgFile);

        $img = Image::make($imgFile);
        $width = ($input->getOption('width')) ? $input->getOption('width') : self::DEFAULT_WIDTH;

        $img->resize($width, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $img->save($imgFile);

        $message = "Image file: {$input->getArgument('image')}, was successfully resized.";
        $output->writeln("<info>{$message}</info>");
    }

    /**
     * @param $imgFile
     * @return void
     */
    protected function verifyImage($imgFile)
    {
        if (! in_array(strtolower(pathinfo($imgFile)['extension']), $this->extensionsArray()) || ! is_file($imgFile)) {
            throw new RuntimeException('Image file does not exist or extension is invalid!');
        }
    }

    /**
     * @return array
     */
    protected function extensionsArray()
    {
        return ['jpg', 'jpeg', 'png'];
    }
}