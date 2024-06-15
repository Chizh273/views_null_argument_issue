
# NULL contextual argument should be skipped during views URL generation

<h3 id="summary-problem-motivation">Problem/Motivation</h3>

I used the <code>NULL</code> contextual argument to skip the default URL argument value for rewriting it later with the custom argument_default plugin but I faced an issue with URL generation. My view except contextual filters also have exposed filters, and the problem is that exposed form action was wrong.

I investigated this issue deeper and found that this problem comes from <a href="https://api.drupal.org/api/drupal/core%21modules%21views%21src%21Plugin%21views%21display%21PathPluginBase.php/function/PathPluginBase%3A%3AgetRoute/10"><code>\Drupal\views\Plugin\views\display\PathPluginBase::getRoute</code></a>. When we generate routes for views we include all contextual arguments into the view display URL path.

<h4 id="summary-steps-reproduce">Steps to reproduce</h4>

I have created <a href="https://github.com/Chizh273/views_null_argument_issue">dummy module</a> that provides node type and view with exposed and contextual filters for reproducing this issue. So, steps to reproduce:

<ol>
  <li>Install the <a href="https://github.com/Chizh273/views_null_argument_issue">module</a> (this module will generate on install 20 nodes needed for reproducing the problem)</li>
  <li>
  Go to the <code>/views-null-argument-issue/{NodeID}</code> (use any Node ID of <code>views_null_argument_issue</code> node type for NodeID) and check that
  <ul>
    <li>the view displays 10 items (result summary above of the form)</li>
    <li>all items have the same type in the table</li>
  </ul>
  </li>
  <li>Submit the exposed form with any filter value</li>
  <li>
  Check the following
  <ul>
    <li>The URL was changed to like this <code>/views-null-argument-issue/{NodeID}/exception-value?...</code></li>
    <li>the view displays 20 items (result summary above of the form)</li>
    <li>there are <code>type 1</code> and <code>type 2</code> items</li>
  </ul>
  </li>
</ol>


<h3 id="summary-proposed-resolution">Proposed resolution</h3>

I think the <code>NULL</code> argument should be excluded from route params in the <a href="https://api.drupal.org/api/drupal/core%21modules%21views%21src%21Plugin%21views%21display%21PathPluginBase.php/function/PathPluginBase%3A%3AgetRoute/10"><code>\Drupal\views\Plugin\views\display\PathPluginBase::getRoute</code></a> to fix this issue.

We can exclude it by creating a new <code>SkipFromRouteParamsInterface</code> interface, and use this interface for filtering all arguments in the <a href="https://api.drupal.org/api/drupal/core%21modules%21views%21src%21Plugin%21views%21display%21PathPluginBase.php/function/PathPluginBase%3A%3AgetRoute/10"><code>\Drupal\views\Plugin\views\display\PathPluginBase::getRoute</code></a> to skip arguments that implement it.

Another way of fixing this is to exclude the <code>NULL</code> argument from the route params by configurations. We can add a new checkbox to the <code>NULL</code> argument configuration form that will be responsible for skipping it from route parameters.

In the end, both of these ideas should filter <code>$argument_ids</code> to exclude the <code>NULL</code> argument (and other arguments that implement either interface or configuration) in the <a href="https://api.drupal.org/api/drupal/core%21modules%21views%21src%21Plugin%21views%21display%21PathPluginBase.php/function/PathPluginBase%3A%3AgetRoute/10"><code>\Drupal\views\Plugin\views\display\PathPluginBase::getRoute</code></a>


