<?php

declare(strict_types=1);

namespace Drupal\views_null_argument_issue\Plugin\views\argument_default;

use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\views\Plugin\views\argument_default\ArgumentDefaultPluginBase;

/**
 * The ViewsNullArgumentIssueType default argument.
 *
 * @ViewsArgumentDefault(
 *   id = "views_null_argument_issue_type",
 *   title = @Translation("Views Null Argument Issue: Type"),
 * )
 */
class ViewsNullArgumentIssueType extends ArgumentDefaultPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getArgument(): ?string {
    if (isset($this->view->args[0])) {
      $node = Node::load($this->view->args[0]);

      if ($node instanceof NodeInterface && $node->hasField('field_views_null_argument_issue')) {
        return $node->get('field_views_null_argument_issue')->value;
      }
    }

    return NULL;
  }

}
